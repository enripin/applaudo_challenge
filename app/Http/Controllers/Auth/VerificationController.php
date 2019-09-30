<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\VerifyEmail;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    public function verifyUser($token)
    {
        $verifyUser = VerifyEmail::where('token', $token)->first();
        if(isset($verifyUser) ){
            $user = $verifyUser->user;
            if($user->verified) {
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

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('User already have verified email!', 422);
//            return redirect($this->redirectPath());
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json('The notification has been resubmitted');
//        return back()->with('resent', true);
    }
}
