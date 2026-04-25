<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'mobile'   => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create user first
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->mobile,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active',
        ]);

        //  AUTO-GENERATE CUSTOMER ID (CIN)
        $user->cin = 'CX' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Signup successful',
            'data' => [
                'id' => $user->id,
                'cin' => $user->cin,
                'name' => $user->name,
                'phone' => $user->phone,
            ],
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by mobile number
        $user = User::where('phone', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered',
            ], 404);
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password',
            ], 401);
        }

        // ✅ GENERATE TOKEN HERE
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role,
            ],
        ], 200);
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
        ]);

        $user = \App\Models\User::where('phone', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered',
            ], 404);
        }

        // Static OTP for now
        $otp = 1234;

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => $otp // ⚠️ remove this in production
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        // Static OTP check
        if ($request->otp != 1234) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
        ]);
    }

    public function resetPassword(Request $request)
    {
        // Validate inputs
        $request->validate([
            'mobile' => 'required|string|exists:users,phone',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Find user by mobile
        $user = User::where('phone', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Reset password
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }


    public function updatePhone(Request $request)
    {
        $user = $request->user(); //  IDENTIFIED ONLY BY TOKEN

        // Validate phone number
        $validator = Validator::make($request->all(), [
            'mobile' => [
                'required',
                'string',
                'max:15',
                // unique except current user
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update phone number
        $user->phone = $request->mobile;
        $user->save();

        $user->tokens()->delete();

        // 🔑 Create fresh token
        $newToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Phone number updated successfully',
            'data' => [
                'id' => $user->id,
                'phone' => $user->phone,
                'token' => $newToken,
            ],
        ], 200);
    }


}
