<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/users/{id_user}/verified', 'UsersController@verifyUser');//Verification link send by email to new users
Route::get('/users/{id_user}/password', 'UsersController@getResetForm');//Display a form to change password
Route::post('/users/{id_user}/password', 'UsersController@doReset')->name('do-reset-password');//Make the password change
