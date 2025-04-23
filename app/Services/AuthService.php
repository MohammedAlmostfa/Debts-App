<?php

namespace App\Services;

use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Handles user login.
     *
     * @param array $credentials User credentials (email and password).
     * @return array Response including the status, message, and token data.
     */
    public function login($credentials)
    {
        try {
            // Fetch the single user's password from the database
            $user = User::first(); // الحصول على المستخدم الأول (المستخدم الوحيد)

            // Check if the provided password matches the hashed password
            if (!Hash::check($credentials['password'], $user->password)) {
                return [
                    'status' => 401, // Unauthorized
                    'message' => ['فشل في تسجيل الدخول. كلمة المرور غير صحيحة.'],
                ];
            }

            // If authentication succeeds, return success message
            return [
                'message' => 'Login successful.', // Success message
                'status' => 201, // HTTP status code for created
                'data' => [
                    'user_id' => $user->id,
                    'name' => $user->name,
                ],
            ];
        } catch (Exception $e) {
            // Log the error details for debugging
            Log::error('Error during login: ' . $e->getMessage());

            return [
                'status' => 500, // Internal Server Error
                'message' => ['حدث خطأ أثناء معالجة الطلب.'],
            ];
        }
    }


    public function resetPassword($data)
    {
        try {
            // Fetch the user
            $user = User::first(); // Assuming there's only one user

            if (!$user) {
                return [
                    'status' => 404, // User not found
                    'message' => ['المستخدم غير موجود.'],
                ];
            }

            // Check if the provided old password matches the hashed password
            if (!Hash::check($data['old_password'], $user->password)) {
                return [
                    'status' => 401, // Unauthorized
                    'message' => ['كلمة المرور القديمة غير صحيحة.'],
                ];
            }

            // Update the password to the new one
            $user->update([
                'password' => Hash::make($data['new_password']), // Hash the new password
            ]);

            return [
                'status' => 200, // Success
                'message' => ['تم تغيير كلمة المرور بنجاح.'],
            ];
        } catch (Exception $e) {
            // Log any error encountered
            Log::error('Error resetting password: ' . $e->getMessage());

            return [
                'status' => 500, // Internal Server Error
                'message' => ['حدث خطأ أثناء معالجة الطلب.'],
            ];
        }
    }



}
