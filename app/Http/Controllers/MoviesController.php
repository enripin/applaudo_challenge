<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\MovieLog;
use App\Http\Resources\MovieResource;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\MovieRequest;

class MoviesController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Movies Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles movies CRUD operations
    |
    */

    //Using middleware to limit access for not logged users
    public function __construct(){
        $this->middleware('jwt', ['except' => ['index','show']]);
    }

    /**
     * Display a listing of the movies. If user is admin (has movies.show-all permission)
     * will return all movies available and unavailable
     * For not admin users it will only return available movies
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        /**Possible Request $request params:
         * (string) token: authorization token
         * (string) available: true/false if want to filter the movies for availability status
         * (string) search: true/false if want to filter the movies for availability status
         * (string) order_by: title/likes_count Field used to order the results
         * (string) direction: asc/desc ascendent/descendent order
         * others: pagination related fields
         */
        $movies=array();

        //Validating if the request has a valid authorization token
        if($request->has("token") && is_null(auth()->user())){
            return response()->json(['error' => 'token is invalid or expired'], 400);
        }

        //Validating user admin permission
        if($request->has("token") && auth()->user()->hasPermissionTo(Permission::findByName('movies.show-all','api'))){
            //If request will be filter by availability
            if($request->has("available")){
                if($request->input('available')=="true"){
                    $movies=Movie::available(true)->withCount('likes');
                }else{
                    $movies=Movie::available(false)->withCount('likes');
                }
            }else{
                $movies=Movie::withCount('likes');
            }
        }else{
            $movies=Movie::available(true)->withCount('likes');
        }

        //Making a search over the title
        if($request->has('search') && $request->input('search')!=""){
            $this->words=explode(' ',$request->input('search'));
            $movies=$movies->where(function($query){
                foreach($this->words as $word){
                    $query->orWhere('title','like','%'.$word.'%');
                }
            });
        }

        //Setting default values to order the results
        $order_by='title';
        $direction='asc';

        if($request->has('order_by') && $request->input('order_by')!='title'){
            $order_by='likes_count';
            $direction='desc';
        }
        if($request->has('direction') && in_array($request->input('direction'),['asc','desc'])){
            $direction=$request->input('direction');
        }

        $n_records=10;
        if($request->has('n_records') && is_int($request->input('n_records'))){
            $n_records=$request->input('n_records');
        }

        $movies=$movies->orderBy($order_by,$direction)->paginate($n_records);
        return MovieResource::collection($movies);
    }

    /**
     * Store a newly created movie in storage.
     *
     * @param  MovieRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MovieRequest $request)
    {
        //Permissions and validations for this method are in MovieRequest $request
        $nMovie=Movie::create([
            'title' => $request->input('title'),
            'description' => ($request->has('description')?$request->input('description'):null),
            'sale_price' => $request->input('sale_price'),
            'rental_price' => $request->input('rental_price'),// rental price by day
            'available' => $request->input('available'),// 0/1 available/unavailable
            'stock' => $request->input('stock'),
            'image' => ($request->has('image')?$request->input('image'):null)
        ]);
        return response()->json([
            'message' => 'Movie created successfully.',
            'data'  =>  $nMovie
        ], 201);
    }

    /**
     * Display the specified movie.
     *
     * @param  int  $id (id_movie)
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $movie=Movie::find($id);
        if(!is_null($movie)){
            return response()->json([
                'data' => $movie
            ], 200);
        }else{
            return response()->json([
                'message' => 'Movie not found.'
            ], 404);
        }
    }

    /**
     * Update the specified movie in storage.
     *
     * @param  MovieRequest  $request
     * @param  int  $id (id_movie)
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MovieRequest $request, $id)
    {
        //Permissions and validations for this method are in MovieRequest $request

        $movie=Movie::find($id);
        if(!is_null($movie)){//If the id_movie was found
            $changes=array();/*Used for the changes log*/
            if($movie->title!=$request->input('title')){
                $changes['prev_title']=$movie->title;
                $changes['new_title']=$request->input('title');
                $movie->title=$request->input('title');
            }
            if($movie->sale_price!=$request->input('sale_price')){
                $changes['prev_sale_price']=$movie->sale_price;
                $changes['new_sale_price']=$request->input('sale_price');
                $movie->sale_price=$request->input('sale_price');
            }
            if($movie->rental_price!=$request->input('rental_price')){
                $changes['prev_rental_price']=$movie->rental_price;
                $changes['new_rental_price']=$request->input('rental_price');
                $movie->rental_price=$request->input('rental_price');
            }
            $movie->description=$request->input('description');
            $movie->image=($request->has('image')?$request->input('image'):null);
            $movie->stock=$request->input('stock');

            $movie->save();

            if(count($changes)>0){//Saving changes in log
                $changes['change_date']=date("Y-m-d H:i:s");
                $changes['id_user']=auth()->user()->id_user;
                $changes['id_movie']=$movie->id_movie;
                MovieLog::create($changes);
            }

            return response()->json([
                'message' => 'Movie updated successfully.',
                'data'  =>  $movie
            ], 200);
        }else{
            return response()->json([
                'message' => 'Movie not found.'
            ], 404);
        }
    }

    /**
     * Making the specified movie available / unavailable for not admin users.
     *
     * @param  Request  $request
     * @param  int  $id (id_movie)
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request, $id)
    {
        //Validating the user permissions
        if(auth()->user()->hasPermissionTo(Permission::findByName('movies.cud','api'))){

            $request->validate([
                'available' => 'required|between:0,1'
            ]);
            $movie=Movie::find($id);
            if(!is_null($movie)){//If the id_movie was found

                $movie->available=$request->input('available');
                $movie->save();

                return response()->json([
                    'message' => 'Movie availability updated successfully.',
                    'data'  =>  $movie
                ], 200);
            }else{
                return response()->json([
                    'message' => 'Movie not found.'
                ], 404);
            }
        }else{
            return response()->json(['error' => 'This action is unauthorized'], 403);
        }

    }

    /**
     * Remove the specified movie from storage.
     *
     * @param  int  $id (id_movie)
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        //Validating users permissions
        if(auth()->user()->hasPermissionTo(Permission::findByName('movies.cud','api'))){
            $movie=Movie::find($id);
            if(!is_null($movie)){
                MovieLog::where('id_movie',$id)->delete();
                if($movie->delete()){
                    return response()->json(['message' => 'Movie deleted'], 200);
                }
            }else{
                return response()->json(['message' => 'Movie not found.'], 404);
            }
        }else{
            return response()->json(['error' => 'This action is unauthorized'], 403);
        }
    }

    /**
     * Save a like record for the specified movie.
     *
     * @param  int  $id (id_movie)
     * @return \Illuminate\Http\JsonResponse
     */
    public function like($id){
        $user=auth()->user();
        $movie=Movie::find($id);
        if(is_null($movie)){
            return response()->json(['message' => 'Movie not found'], 404);
        }
        $movie->likes()->sync([$user->id_user]);
        return response()->json(['message' => 'Like saved successfully'], 201);
    }
}
