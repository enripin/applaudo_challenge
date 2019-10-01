<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\ResetPasswordRequestNotification;
use App\Notifications\ResetPasswordSuccessNotification;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    /**
     * Create token password reset
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user)
            return response()->json([
                'message' => "We can't find a user with the given email address."
            ], 404);
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email], ['email' => $user->email,'token' => sha1(time())]
        );
        if ($user && $passwordReset){
            $user->notify(
                new ResetPasswordRequestNotification($passwordReset->token)
            );
        }
        return response()->json([
            'message' => 'Recovery email sent.'
        ], 200);
    }

    public function getResetForm($token){
        $passwordReset = PasswordReset::where('token', $token)
            ->first();
        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }
        return view('password.reset', compact('token'));
    }

    /**
     * Reset password
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doReset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'token' => 'required|string'
        ]);
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();
        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user)
            return response()->json([
                'message' => "We can't find a user with that e-mail address."
            ], 404);

        $user->password = Hash::make($request->password);
        $user->save();
        $passwordReset->delete();

        $user->notify(new ResetPasswordSuccessNotification());

        return response()->json([
            'message' => 'Password updated successfully'
        ], 200);
    }
}
