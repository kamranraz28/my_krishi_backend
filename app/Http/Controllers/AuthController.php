<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

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
}
