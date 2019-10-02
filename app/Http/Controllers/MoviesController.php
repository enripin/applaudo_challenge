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
    public function __construct(){
        $this->middleware('jwt', ['except' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return MovieResource
     */
    public function index(Request $request)
    {
        $movies=array();
        if($request->has("token") && auth()->user()->hasPermissionTo(Permission::findByName('movies.show-all','api'))){
            if($request->has("available")){
                $movies=Movie::available((bool)$request->input('available'))->withCount('likes');
            }else{
                $movies=Movie::withCount('likes');
            }
        }else{
            $movies=Movie::available(true)->withCount('likes');
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

        $movies=$movies->orderBy($order_by,$direction)->paginate(10);
        return MovieResource::collection($movies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  MovieRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MovieRequest $request)
    {
        //Permissions and validations for this method are MovieRequest $request
        $nMovie=Movie::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'sale_price' => $request->input('sale_price'),
            'rental_price' => $request->input('rental_price'),
            'available' => $request->input('available'),
            'stock' => $request->input('stock')
        ]);
        return response()->json([
            'message' => 'Movie created successfully.',
            'data'  =>  $nMovie
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
        $movie=Movie::find($id);
        if(!is_null($movie)){
            return response()->json([
                'data' => MovieResource::collection($movie)
            ], 200);
        }else{
            return response()->json([
                'message' => 'Movie not found.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  MovieRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MovieRequest $request, $id)
    {
        $movie=Movie::find($id);
        if(!is_null($movie)){
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
            $movie->available=$request->input('available');
            $movie->description=$request->input('description');
            $movie->stock=$request->input('stock');

            $movie->save();

            if(count($changes)>0){
                $changes['change_date']=date("Y-m-d H:i:s");
                $changes['id_user']=auth()->user()->id_user;
                $changes['id_movie']=$movie->id_movie;
                MovieLog::create($changes);
            }

            return response()->json([
                'message' => 'Movie created successfully.',
                'data'  =>  $movie
            ], 201);
        }else{
            return response()->json([
                'message' => 'Movie not found.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request, $id)
    {
        //Validating the user permissions
        if(auth()->user()->hasPermissionTo(Permission::findByName('movies.cud','api'))){

            $request->validate([
                'available' => 'required|between:0,1'
            ]);
            $movie=Movie::find($id);
            if(!is_null($movie)){

                $movie->available=$request->input('available');
                $movie->save();

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

    }

    /**
     * Remove the specified movie from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
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
            return response()->json(['error' => 'This action is unauthorized'], 401);
        }
    }
}