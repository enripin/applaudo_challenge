<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use App\Models\VerifyEmail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\ResetPasswordRequestNotification;
use App\Notifications\ResetPasswordSuccessNotification;
use Carbon\Carbon;

class UsersController extends Controller
{

    /*
   |--------------------------------------------------------------------------
   | Users Controller
   |--------------------------------------------------------------------------
   |
   | This controller handles Users registration, verification, change of role and password reset
   |
   */



    /**
     * Create token password reset and send it by email
     *
     * @param  Request $request
     * @param  integer $id_user
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request, $id_user)
    {
        //Ask id_user and email for security
        $user = User::where('id_user',$id_user)->where('email',$request->input('email'))->first();
        if (!$user)
            return response()->json([
                'message' => "We can't find the requested user."
            ], 404);
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email], ['email' => $user->email,'token' => sha1(time())]
        );
        if ($user && $passwordReset){
            $user->notify(
                new ResetPasswordRequestNotification($passwordReset->token, $user->id_user)
            );
        }
        return response()->json([
            'message' => 'Recovery email sent.'
        ], 200);
    }

    /**
     * Returns view for change of password
     *
     * @param  Request $request
     * @param  integer $id_user
     * @return view
     */
    public function getResetForm(Request $request, $id_user){
        $token=$request->input('token');
        $user=User::find($id_user);
        if(is_null($user)){
            return response()->json([
                'message' => 'Invalid url.'
            ], 404);
        }
        $passwordReset = PasswordReset::where('email',$user->email)->where('token', $token)
            ->first();
        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 403);
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 403);
        }
        return view('password.reset', compact('token','user'));
    }

    /**
     * Reset password
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doReset(Request $request, $id_user)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'token' => 'required|string'//Password reset token sent by email
        ]);
        $passwordReset = PasswordReset::where([
            ['token', $request->input('token')],
            ['email', $request->input('email')]
        ])->first();

        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 403);

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'This password reset token has expired.'
            ], 403);
        }

        $user = User::where('email', $passwordReset->email)->where('id_user',$id_user)->first();
        if (!$user){
            return response()->json([
                'message' => "We can't find a user with that e-mail address."
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $passwordReset->delete();

        $user->notify(new ResetPasswordSuccessNotification());

        return response()->json([
            'message' => 'Password updated successfully'
        ], 200);
    }

    /**
     * Update the verified status of a newly register user
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id_user
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUser(Request $request, $id_user)
    {
        $token=$request->token;
        $verifyUser = VerifyEmail::where('token', $token)->where('id_user',$id_user)->first();
        if(isset($verifyUser) ){//Validating token and id_user
            $user = $verifyUser->user;
            if(!$user->verified) {
                $verifyUser->user->email_verified_at = date('Y-m-d H:i:s');
                $verifyUser->user->save();
                return response()->json(['message'=>'Your e-mail is verified. You can now use your credential with the API.'],200);
            } else {
                return response()->json(['message'=>'Your e-mail is already verified.'],400);
            }
        } else {
            return response()->json(['message'=>'Your e-mail could not be verified'],400);
        }
    }

    /**
     * Resend verification email to a given user
     *
     * @param  int $id_user
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerification($id_user)
    {
        $user=User::find($id_user);
        if(!is_null($user)){//Validating id_user
            if($user->verified){
                return response()->json(['message'=>'User already have verified email!'], 400);
            }else{
                $user->notify(
                    new VerifyEmailNotification($user)
                );
                return response()->json(['message'=>'The verification email has been resubmitted'],200);
            }
        }else{
            return response()->json(['message'=>'User not found!'], 404);
        }
    }

    /**
     * Create a new user in storage and send a validation email.
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

        if (User::where('email', '=', $email)->exists()) {//Checking if email is already registered
            return response()->json(['message' => 'Email already used'], 400);
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
     * Change the rol of the given user.
     *
     * @param  Request  $request
     * @param  int  $id_user
     * @return \Illuminate\Http\Response
     */
    public function changeRole(Request $request, $id_user)
    {
        if(is_null(auth()->user()) || !auth()->user()->hasPermissionTo(Permission::findByName('users.change-role','api'))){
            return response()->json(['error' => 'This action is unauthorized'], 401);
        }

        $user=User::find($id_user);
        if(is_null($user)){//Validating user
            return response()->json(['message' => 'User not found'], 404);
        }

        $role=Role::where('name',$request->input('role'))->first();
        if(is_null($role)){//Validating role
            return response()->json(['message' => 'Role not found'], 404);
        }

        foreach($user->roles as $rol){
            $user->removeRole($rol);
        }
        $user->assignRole($role);

        return response()->json(['message' => 'Role changed successfully'], 200);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
