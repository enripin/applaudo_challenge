<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Http\Resources\MovieResource;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\MovieRequest;

class MoviesController extends Controller
{
    public function __construct(){
        $this->middleware('jwt', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return App\Http\Resources\MovieResource
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
        $movies=$movies->orderBy('likes_count','desc')->paginate(10);
        return MovieResource::collection($movies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MovieRequest $request)
    {
        //Permissions for this method are MovieRequest $request
        Movie::created([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'sale_price' => $request->input('sale_price'),
            'rental_price' => $request->input('rental_price'),
            'availability' => $request->input('availability')
        ]);
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
