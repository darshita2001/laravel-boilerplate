<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class JwtVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {

            $user = auth('api')->user();
            // $userExists = JWTAuth::parseToken()->authenticate();

            if (empty($user)) {
                return failureResponse(__('messages.user_not_found'), 404);
            }

            return $next($request);
        } catch (Exception $e) {
            
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {

                return failureResponse(__('auth.invalid_token'), JsonResponse::HTTP_UNAUTHORIZED);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {


                return failureResponse(__('auth.token_expired'), JsonResponse::HTTP_UNAUTHORIZED);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {

                return failureResponse(__('auth.token_blacklisted'), JsonResponse::HTTP_UNAUTHORIZED);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {

                return failureResponse(__('auth.token_not_found'), JsonResponse::HTTP_NOT_FOUND);
            }

            return failureResponse(__('auth.unauthorized_access'), JsonResponse::HTTP_UNAUTHORIZED);
        }
    }
}
