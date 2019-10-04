<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use \Tymon\JWTAuth\Exceptions\TokenExpiredException;
use \Tymon\JWTAuth\Exceptions\TokenInvalidException;
use \Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //Customizing messages for some exceptions
        if ($exception instanceof TokenExpiredException) {
            return response()->json(['message' => 'token is expired'], 401);
        } elseif ($exception instanceof TokenInvalidException) {
            return response()->json(['message' => 'token is invalid'], 401);
        } elseif ($exception instanceof JWTException) {
            return response()->json(['message' => 'token absent'], 401);
        } elseif ($exception instanceof AuthorizationException){
            return response()->json(['message' => 'This action is unauthorized'], 401);
        }

        return parent::render($request, $exception);
    }
}
