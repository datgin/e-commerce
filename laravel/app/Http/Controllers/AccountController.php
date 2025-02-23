<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;

use App\Mail\Verify\SendMail;
use App\Events\RegisterSucess;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AccountController extends Controller
{
    public function register(LoginRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new RegisterSucess($user));

        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'Retrieved successfully'
        ]);
    }


    public function login(LoginRequest $request)
    {
        return transaction(function () use ($request) {
            $credentials = $request->validated();

            if (!$token = JWTAuth::attempt($credentials)) {
                return errorResponse('Tài khoản hoặc mật khẩu không chính xác!', 401);
            }

            $expiresIn = config('jwt.ttl') * 60;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => auth()->user(),
                'message' => 'Đăng nhập thành công.',
                'expires_in' => $expiresIn,
            ]);
        });
    }

    public function refreshToken(Request $request)
    {
        try {
            // Lấy token mới
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            $expiresIn = config('jwt.ttl') * 60;

            return response()->json([
                'status' => 'success',
                'token' => $newToken,
                'expires_in' => $expiresIn, // Thời gian hết hạn tính bằng giây
            ], 200);
        } catch (JWTException $e) {
            logInfo($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Token is invalid or expired',
            ], 401);
        }
    }


    public function verify($id)
    {
        $user = User::findOrFail($id);

        if ($user && $user->email_verified_at == null) {
            $user->email_verified_at = now();
            $user->save();
            return response()->json(['message' => 'Email verified successfully']);
        }
    }

    public function me(Request $request)
    {
        try {
            // Lấy thông tin người dùng từ token
            $user = JWTAuth::parseToken()->authenticate();

            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Token is invalid or expired:' . $e->getMessage(),
            ], 401);
        }
    }
}
