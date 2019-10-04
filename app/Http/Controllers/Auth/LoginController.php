<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | generates access token. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //Using middleware to limit access for not logged users
    public function __construct(){
        $this->middleware('jwt', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     * @param Request $request (request has to contain email and password for the user)
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){

        //Validating fields
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $email=$request->input('email');
        $password=$request->input('password');
        $credentials = array("email"=>$email,"password"=>$password);
        if (!$token = auth('api')->attempt($credentials)) {//Validating user and password to generate JWT token
            return response()->json(['message' => 'Unauthorized'], 401);
        }else{//If the user credentials are right

            //Checking is the account has been already verified by email
            $user=User::where("email",$email)->first();
            if(!$user->verified){
                return response()->json(['message' => 'Account not verified'], 401);
            }
        }
        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the authentication token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(){
        //A token has to be send in the request
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    /**
     * Refresh a token (generates a new token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(){
        //A token has to be send in the request
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the authenticated User information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(){
        //A token has to be send in the request
        return response()->json(auth('api')->user(), 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token (authorization token)
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], 200);
    }
}
