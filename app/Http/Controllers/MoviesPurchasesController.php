<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\MoviePurchase;
use App\Models\PurchaseDetail;
use App\Http\Resources\PurchaseResource;

class MoviesPurchasesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Movies Purchases Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles movies purchases CRUD operations
    | At this moment only create operation has been implemented
    |
    */

    //Using middleware to limit access for not logged users
    public function __construct(){
        $this->middleware('jwt');
    }

    /**
     * Store a newly created movie purchase in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        /* $request must contain:
         * (string) token: User's authorization token
         * (array) movies('(int)movie_id'=>'(int)number of copies to be purchased')
         */

        $movies=$request->input('movies');
        if(!is_array($movies) || count($movies)==0){//Validating movies field to be a valid array
            return response()->json(['error' => 'Bad request'], 400);
        }

        //Checking if all movies in array are available and with enough stock
        foreach($movies as $id_movie => $number){
            $movie_info=Movie::find($id_movie);
            if(is_null($movie_info) || $movie_info->available==0 || $movie_info->stock<=$number){
                return response()->json(['error' => 'Movie not found, unavailable or with not enough stock'], 400);
            }
        }

        //Creating movie purchase to generate id_purchase
        $movie_purchase=new MoviePurchase();
        $movie_purchase->purchase_date=date('Y-m-d H:i:s');
        $movie_purchase->id_user=auth()->user()->id_user;
        $movie_purchase->save();

        $total_payment=0;//Will be used to calculate the total payment
        foreach($movies as $id_movie => $number){//Generating details of purchase
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
