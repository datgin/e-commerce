<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Kiểm tra tính hợp lệ của token
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token is invalid or expired'], 401);
        }

        // Tạo token mới
        $newToken = JWTAuth::refresh(JWTAuth::getToken());

        // Thêm token mới vào response header
        $response = $next($request);
        $response->headers->set('Authorization', 'Bearer ' . $newToken);

        return $response;
    }
}
