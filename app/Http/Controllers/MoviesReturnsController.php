<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovieReturn;
use App\Models\MovieRental;
use Carbon\Carbon;

class MoviesReturnsController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Movies Returns Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles movies rentals CRUD operations
    | At this moment only create operation has been implemented
    |
    */

    //Using middleware to limit access for not logged users
    public function __construct(){
        $this->middleware('jwt');
    }

    /**
     * Store a newly created movie return in storage.
     *
     * @param  Request  $request
     * @param  int $id_movie
     * @param  int $id_rental
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $id_movie, $id_rental)
    {

        $rental=MovieRental::find($id_rental);
        if(!is_null($rental)){
            if($rental->state=='p'){//Validating if the movie has not been returned

                $nReturn=new MovieReturn();
                $nReturn->id_rental=$id_rental;
                $nReturn->return_date=date("Y-m-d H:i:s");//The date and hour the movie was returned

                //Calculating if there has been delay in return
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
                    ], 201);

                }
            }else{
                return response()->json([
                    'message' => 'Movie already returned.'
                ], 400);
            }
        }else{
            return response()->json([
                'message' => 'Rental record not found.'
            ], 404);
        }
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
