<?php

namespace App\Http\Controllers;

use App\Models\MovieRental;
use Illuminate\Http\Request;
use App\Models\MovieLog;
use App\Models\Movie;
use Carbon\Carbon;

class MoviesRentalsController extends Controller
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
    public function store(Request $request, $id_movie)
    {
        //The rental price will be calculated from the daily rental price and the number of days the movie will be rented

        $request->validate([
            'return_date' => 'required|date|after:now'
        ]);
        $movie=Movie::find($id_movie);
        if(!is_null($movie)){
            if($movie->available==1 && $movie->stock>=1){

                $date = Carbon::parse($request->input('return_date'));
                $now = Carbon::now();

                $diff = $date->diffInDays($now)+1;

                $nRental=MovieRental::create([
                    'rent_date' => date("Y-m-d H:i:s"),
                    'return_date' => $request->input('return_date'),
                    'payment' => ($movie->rental_price*$diff),
                    'rental_price' => $request->input('rental_price'),
                    'id_movie' => $id_movie,
                    'id_user'   => auth()->user()->id_user
                ]);

                //Updating the availability field to show the available copies
                $movie->stock--;//Decreasing the number of available copies
                $movie->save();

                return response()->json([
                    'message' => 'Movie rental created successfully.',
                    'data'  =>  $nRental
                ], 201);
            }else{
                return response()->json([
                    'error' => 'Movie not available.'
                ], 400);
            }
        }else{
            return response()->json([
                'error' => 'Movie not found.'
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
