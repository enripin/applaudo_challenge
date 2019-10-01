<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\VerifyEmail;
use Spatie\Permission\Models\Role;
use App\Notifications\VerifyEmailNotification;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation.
    |
    */


    /**
     * Create a new user and send a validation email.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string'
        ]);

        $email=$request->input('email');

        if (User::where('email', '=', $email)->exists()) {
            return response()->json(['error' => 'Email already used'], 400);
        }else{
            $nUser=User::create([
                'first_name'=>$request->input('first_name'),
                'last_name'=>$request->input('last_name'),
                'password'=>Hash::make($request->input('password')),
                'email'=>$email,
            ]);

            $nUser->assignRole(Role::findByName('client', 'api'));

            //Sending verification link to email
            $verifyUser = VerifyEmail::create([
                'id_user' => $nUser->id_user,
                'token' => sha1(time())
            ]);
            $nUser->notify(
                new VerifyEmailNotification($nUser)
            );

            return response()->json(['message' => 'User created. A verification link was sent to the email '.$nUser->email], 201);
        }
    }
}
