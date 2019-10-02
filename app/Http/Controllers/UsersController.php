<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\VerifyEmail;
use Spatie\Permission\Models\Role;
use App\Notifications\VerifyEmailNotification;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function verifyUser($token)
    {
        $verifyUser = VerifyEmail::where('token', $token)->first();
        if(isset($verifyUser) ){
            $user = $verifyUser->user;
            if(!$user->verified) {
                $verifyUser->user->email_verified_at = now();
                $verifyUser->user->save();
                $status = "Your e-mail is verified. You can now use your credential with the API.";
            } else {
                $status = "Your e-mail is already verified. You can now use your credential with the API.";
            }
        } else {
            $status="Sorry your email cannot be identified.";
        }
        echo $status;
    }

    public function resendVerification($id_user)
    {
        $user=User::find($id_user);
        if(!is_null($user)){
            if($user->verified){
                return response()->json('User already have verified email!', 422);
            }else{
                $user->notify(
                    new VerifyEmailNotification($user)
                );
                return response()->json('The notification has been resubmitted',200);
            }
        }else{
            return response()->json('User not found!', 404);
        }
    }

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
