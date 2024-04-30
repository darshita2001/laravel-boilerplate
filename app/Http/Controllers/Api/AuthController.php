<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\AuthRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class AuthController extends Controller implements HasMiddleware
{
    /**
     * Middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('jwt.verify', except: ['register', 'login']),
        ];
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            $token = auth()
                ->guard('api')
                ->attempt($credentials);

            if (!$token) {
                return failureResponse(__('auth.failed '));
            }

            $user = User::where('email', $request->email)
                ->first();

            if ($token && $user) {
                $data = [
                    'token' => $token,
                ];

                return successResponseWithData($data, __('auth.login_successful'));
            }

            return failureResponse(__('messages.user_not_found'));

        } catch (Throwable  $th) {
            errorLogger('AuthController@login ', $th);
            return failureResponse($th->getMessage());
        }
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request)
    {
        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $data = [
                'user' => $user,
            ];

            return successResponseWithData($data, __('messages.user_registered'), JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            errorLogger('AuthController@register ', $th);
            return failureResponse($th->getMessage());
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        auth('api')->logout();
        JWTAuth::invalidate($request->bearerToken());

        return successResponse(__('auth.logout_successful'));
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $data = [
            'token' => JWTAuth::refresh(),
        ];

        return successResponseWithData($data, __('auth.token_refreshed'));
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        try {

            $user = auth()->user();

            $data = [
                'user' => $user,
            ];

            return successResponseWithData($data, __('messages.user_retrieved'));
        } catch (Throwable $th) {
            errorLogger('AuthController@userProfile ', $th);
            return failureResponse($th->getMessage());
        }
    }
}
