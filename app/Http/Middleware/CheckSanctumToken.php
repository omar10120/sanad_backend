<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CheckSanctumToken
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            $array=[
                'data' => null,
                'message' => 'token not found!',
                'status' => 401,
            ];
            return response($array, 401);
        }

        $token = substr($authHeader, 7);

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            $array=[
                'data' => null,
                'message' => 'Invalid token!',
                'status' => 401,
            ];
            return response($array, 401);
        }

        $user = $accessToken->tokenable;

        if($user->status == 0){
            $array=[
                'data' => null,
                'message' => trans('auth.inactive_account'),
                'status' => 401,
            ];
            return response($array, 401);
        }
        Auth::setUser($user);

        return $next($request);
    }
}
