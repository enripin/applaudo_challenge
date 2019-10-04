<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\RoleResource;

class RolesAndPermissionsController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Movies Returns Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles roles and permissions CRUD operations
    | Right now there are only two roles(admin and client) in seeders
    | and only admin has permissions added
    | At this moment only show operation has been implemented for roles and permissions
    |
    */

    //Using middleware to limit access for not logged users
    public function __construct(){
        $this->middleware('jwt');
    }

    /**
     * Display a listing of the roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexRoles()
    {
        if(auth()->user()->hasPermissionTo(Permission::findByName('users.change-role','api'))){
            return RoleResource::collection(Role::all());
        }
    }

    /**
     * Display a listing of the permissions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexPermissions()
    {
        if(auth()->user()->hasPermissionTo(Permission::findByName('users.change-role','api'))){
            return Permission::all();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
