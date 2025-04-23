<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Kyojin\JWT\Facades\JWT;

class AuthService
{
    /**
     * Handle user login.
     *
     * @param array $credentials User credentials (email and password).
     * @return array Response with status, message, and token data.
     */
    public function login($credentials)
    {
        try {
            // Attempt to authenticate the user using JWT
            if (!$token = JWT::attempt($credentials)) {
                return [
                    'status' => 401, // Unauthorized
                    'message' => [
                        'errorDetails' => [__('auth.login_failed')],
                    ],
                ];
            }

            // If authentication succeeds
            $user = Auth::user();
            return [
                'message' => __('auth.login_success'),
                'status' => 201, // Created
                'data' => [
                    'token' => $token, // Return the generated token
                    'type' => 'bearer', // Token type
                ],
            ];

        } catch (Exception $e) {
            // Log the error
            Log::error('Error in login: ' . $e->getMessage());
            return [
                'status' => 500, // Internal Server Error
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }

    /**
     * Logout the authenticated user.
     *
     * @return array Contains message and status.
     */
    public function logout()
    {
        try {
            // Logout the user
            Auth::logout();
            return [
                'message' => __('auth.logout_success'),
                'status' => 200, // Success
            ];
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in logout: ' . $e->getMessage());
            return [
                'status' => 500, // Internal Server Error
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }

    /**
     * Refresh the JWT token for the authenticated user.
     *
     * @return array Contains message, status, and refreshed token data.
     */
    public function refresh()
    {
        try {
            // Refresh the JWT token
            $newToken = JWT::parseToken()->refresh();

            return [
                'message' => __('auth.token_refresh_success'),
                'status' => 200, // Success
                'data' => [
                    'user' => auth()->user(),
                    'token' => $newToken,
                ],
            ];

        } catch (Exception $e) {
            // Log the error
            Log::error('Error in token refresh: ' . $e->getMessage());
            return [
                'status' => 500, // Internal Server Error
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }
}
