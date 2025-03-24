<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Http;
use App\Events\OtpRequested;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid email or password'], 401);
        }

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User logged in successfully',
            'token' => $token,
            'user' => $user
        ]);
    }

    // Logout function (delete token)
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Get Authenticated User
    public function me()
    {
        return response()->json(auth()->user());
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:11'
        ]);

        $otp = random_int(100000, 999999);
        $hashedOtp = Hash::make($otp);

        // Update or create the user
        $user = User::updateOrCreate(
            ['phone' => $request->phone], // Search by phone
            ['password' => $hashedOtp, 'level' => 200] // Update or create
        );

        // If the user is newly created, assign a unique_id
        if (!$user->wasRecentlyCreated) {
            // Only update unique_id if it's NULL (i.e., not assigned before)
            if (empty($user->unique_id)) {
                $user->update([
                    'unique_id' => 'MKIN' . str_pad($user->id, 2, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // Dispatch the OTP event
        event(new OtpRequested($user, $otp));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'phone' => $request->phone
        ], 200);
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:11',
            'otp' => 'required|digits:6'
        ]);

        // Find user by phone number
        $user = User::where('phone', $request->phone)->first();

        // Check if user exists and OTP is correct
        if (!$user || !Hash::check($request->otp, $user->password)) {
            return response()->json(['error' => 'Invalid OTP'], 401);
        }

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully',
            'token' => $token,
            'user' => $user
        ]);
    }


}
