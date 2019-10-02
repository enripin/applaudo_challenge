<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'roles'], function () {
    Route::get('/', 'RolesController@index');//Returns a list of roles
    Route::get('{id_role}', 'RolesController@show');//Returns a single role information
});

Route::group(['prefix' => 'users'], function () {
    Route::post('/', 'Auth\UsersController@register');//Register a new user in the application (also send a verification email)
    Route::get('{id_user}/verification', 'UserController@resendVerification');//Resend the verification email
    Route::post('password', 'Auth\ResetPasswordController@sendResetLinkEmail');//Creates a reset token and send an reset url by email
    Route::patch('password', 'Auth\ResetPasswordController@doReset');//Update the user's password
    Route::put('{id_user}/role', 'UsersController@changeRole');//Update the user's role
});

Route::group(['prefix' => 'users/login'], function () {
    Route::get('/', 'Auth\LoginController@me');//Returns user information given authorization token
    Route::post('/', 'Auth\LoginController@login');//Authenticate credentials and returns an authorization token
    Route::delete('/', 'Auth\LoginController@logout');//Make the authorization token invalid
    Route::put('/', 'Auth\LoginController@refresh');//Returns another authorization token
});

Route::group(['prefix' => 'movies'], function () {
    Route::get('/', 'MoviesController@index');//Show a list of movies
    Route::post('/', 'MoviesController@store');//Creates a new movie
    Route::post('/{id_movie}', 'MoviesController@show');//Show a specific movie
    Route::put('/{id_movie}', 'MoviesController@update');//Update a specific movie
    Route::delete('/{id_movie}', 'MoviesController@destroy');//Delete a specific movie
    Route::patch('/{id_movie}/available', 'MoviesController@remove');//Change the available field of the movie
});

Route::group(['prefix' => 'movies/{id_movie}/rentals'], function () {
    Route::post('/', 'MoviesRentalsController@store');//Creates a new movie rental record
});

Route::group(['prefix' => 'movies/{id_movie}/rentals/{id_rental}/return'], function () {
    Route::post('/', 'MoviesReturnsController@store');//Creates a new movie return record
});

Route::group(['prefix' => 'movies/{id_movie}/purchases'], function () {
    Route::post('/', 'MoviesPurchasesController@store');//Creates a new movie purchase record
});