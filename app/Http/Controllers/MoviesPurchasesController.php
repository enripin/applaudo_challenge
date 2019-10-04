<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\MoviePurchase;
use App\Models\PurchaseDetail;
use App\Http\Resources\PurchaseResource;

class MoviesPurchasesController extends Controller
{

    public function __construct(){
        $this->middleware('jwt');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $movies=$request->input('movies');
        if(!is_array($movies) || count($movies)==0){
            return response()->json(['error' => 'Bad request'], 400);
        }

        //Checking if all movies in array are available
        foreach($movies as $id_movie => $number){
            $movie_info=Movie::find($id_movie);
            if(is_null($movie_info) || $movie_info->available==0 || $movie_info->stock<=$number){
                return response()->json(['error' => 'Movie not found, unavailable or with not enough stock'], 400);
            }
        }

        $movie_purchase=new MoviePurchase();
        $movie_purchase->purchase_date=date('Y-m-d H:i:s');
        $movie_purchase->id_user=auth()->user()->id_user;
        $movie_purchase->save();

        $total_payment=0;
        foreach($movies as $id_movie => $number){
            $movie_info=Movie::find($id_movie);
            $detail=new PurchaseDetail();
            $detail->id_purchase=$movie_purchase->id_purchase;
            $detail->id_movie=$movie_info->id_movie;
            $detail->unity_price=$movie_info->sale_price;
            $detail->number=$number;
            $detail->save();
            $total_payment+=((int)$number*$movie_info->sale_price);

            //Updating movie stock
            $movie_info->stock-=$number;
            $movie_info->save();
        }

        $movie_purchase->total_payment=$total_payment;
        $movie_purchase->save();
        return response()->json([
            'message' => 'Purchase processed successfully',
            'data'  => new PurchaseResource($movie_purchase)
        ], 201);
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
