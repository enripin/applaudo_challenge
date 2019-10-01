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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth',], function () {
    Route::post('login', 'Auth\LoginController@login')->name('login');
    Route::post('logout', 'Auth\LoginController@logout');
    Route::post('refresh', 'Auth\LoginController@refresh');
    Route::post('me', 'Auth\LoginController@me');
    Route::post('register', 'Auth\RegisterController@register');
});

Route::group(['prefix' => 'users',], function () {
    Route::post('password', 'Auth\ResetPasswordController@sendResetLinkEmail');
    Route::patch('password', 'Auth\ResetPasswordController@doReset');
});

Route::resource('movies','MoviesController');