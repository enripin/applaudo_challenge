<?php

namespace App\Http\Controllers;

use App\Models\MovieRental;
use Illuminate\Http\Request;
use App\Models\MovieLog;
use App\Models\Movie;
use Carbon\Carbon;

class MoviesRentalsController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Movies Rentals Controller
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
     * Store a newly created rentals in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $id_movie)
    {
        /* $request must contain:
         * (string) token: User's authorization token
         * (date) return_date: Date the client has to return the movie
         * The rental payment will be calculated from the daily rental price and the number of days the movie will be rented
         */


        $request->validate([
            'return_date' => 'required|date|after:now'
        ]);

        $movie=Movie::find($id_movie);
        if(!is_null($movie)){//Validating the id_movie
            if($movie->available==1 && $movie->stock>=1){//Validating the movie is available and has stocks

                $date = Carbon::parse($request->input('return_date'));
                $now = Carbon::parse(date('Y-m-d'));

                $diff = $date->diffInDays($now);//Number of days the movie will be rented

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
                    'message' => 'Movie not available or without stock.'
                ], 400);
            }
        }else{
            return response()->json([
                'error' => 'Movie not found.'
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
