<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovieReturn;
use App\Models\MovieRental;
use Carbon\Carbon;

class MoviesReturnsController extends Controller
{

    public function __construct(){
        $this->middleware('jwt');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id_movie, $id_rental)
    {

        $rental=MovieRental::find($id_rental);
        if(!is_null($rental)){
            if($rental->state=='p'){//If the movie has not been returned

                $nReturn=new MovieReturn();
                $nReturn->id_rental=$id_rental;
                $nReturn->return_date=date("Y-m-d H:i:s");//The date and hour the movie was returned

                $return_date=Carbon::parse($rental->return_date);
                $now=Carbon::parse(date('Y-m-d'));
                $days=$now->diffInDays($return_date,false);

                if($days<0){//If the movie was returned delayed the settled date
                    $nReturn->status='d';
                    //Calculating the penalty of the number of delayed days
                    $nReturn->penalty=abs($days)*$rental->movie->rental_price;
                }

                if($nReturn->save()){//Saving the return record and if it is successfully updating related models states

                    //We save the new status in the rental record
                    $rental->state='r';
                    $rental->save();

                    //Updating the availability number in the movies table
                    $movie=$rental->movie;
                    $movie->stock++;
                    $movie->save();

                    return response()->json([
                        'message' => 'Movie returned successfully.',
                        'data'  => $nReturn
                    ], 400);

                }
            }else{
                return response()->json([
                    'error' => 'Movie already returned.'
                ], 400);
            }
        }else{
            return response()->json([
                'error' => 'Rental record not found.'
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
