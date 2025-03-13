<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            // Check if the token is valid
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            try {
                // If the token is expired, attempt to refresh it
                $newToken = JWTAuth::refresh();
                return response()->json([
                    'message' => 'Token refreshed',
                    'token' => $newToken
                ]);
            } catch (JWTException $e) {
                return response()->json(['error' => 'Token refresh failed, please log in again'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}
