<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Http;

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

        // Generate a unique 6-digit OTP
        $otp = random_int(100000, 999999);

        // Hash the OTP before storing
        $hashedOtp = Hash::make($otp);

        // Find or create user
        $user = User::updateOrCreate(
            ['phone' => $request->phone],
            [
                'password' => $hashedOtp, // Store OTP as password
                'level' => 200, // Insert level 200
            ]
        );


        // Fetch credentials from .env
        $apiKey = config('services.sms.api_key');
        $senderId = config('services.sms.sender_id');
        $smsApiUrl = config('services.sms.api_url');

        // Construct message
        $message = "My Krishi OTP code is: $otp";

        // Send OTP via SMS API
        $response = Http::get($smsApiUrl, [
            'api_key' => $apiKey,
            'type' => 'text',
            'number' => $request->phone,
            'senderid' => $senderId,
            'message' => $message
        ]);

        // Check if SMS was sent successfully
        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'phone' => $request->phone
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP'
            ], 500);
        }
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
