<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'created_at' => $user->created_at,
                        'email_verified_at' => $user->email_verified_at
                    ]
                ]
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request){
        // Check if user is authenticated
        if (auth("sanctum")->user()) {
            auth("sanctum")->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Successfully logged out'], 200);
        }
        
        return response()->json(['message' => 'No active session'], 401);
    }
}
