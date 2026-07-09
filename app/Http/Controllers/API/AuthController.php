<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $request->phone;
        // Find or create user
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            // Auto create mock customer for demo purposes
            $user = User::create([
                'name' => 'Concert Attendee (' . substr($phone, -4) . ')',
                'email' => 'phone_' . $phone . '@justfeast.com',
                'phone' => $phone,
                'role' => 'customer',
                'password' => Hash::make('password'),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully to ' . $phone . ' (Use verification code 1234)',
            'phone' => $phone,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string',
        ]);

        if ($request->code !== '1234') {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP code. Please enter 1234 for the demo.',
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.',
            ], 404);
        }

        // Generate simple token for demo or return details
        return response()->json([
            'status' => 'success',
            'message' => 'Authentication successful',
            'user' => $user,
            'token' => 'mock_token_' . $user->id,
        ]);
    }

    // Role switcher endpoint to easily sign in as any seeded user
    public function loginAs(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found. Run database seeders first.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => 'mock_token_' . $user->id,
        ]);
    }
}
