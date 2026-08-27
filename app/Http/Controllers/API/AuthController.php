<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * POST /api/auth/login
     * Accept a phone number, generate + send an OTP via SMS.
     * NOTE: User account creation is deferred until phone number verification.
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:9|max:20',
        ]);

        $phone = $request->phone;

        // Generate cryptographic OTP and dispatch via SMS service
        $this->otpService->generateAndSend($phone);

        return response()->json([
            'status'  => 'success',
            'message' => 'Verification code sent via SMS to ' . $phone . '.',
            'phone'   => $phone,
        ]);
    }

    /**
     * POST /api/auth/verify
     * Verify the OTP code. On successful verification, create/retrieve the User account
     * and issue a Sanctum token named with the client's User-Agent.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string|size:6',
        ]);

        $phone = $request->phone;
        $code  = $request->code;

        $verification = $this->otpService->verify($phone, $code);

        if (!$verification['success']) {
            return response()->json([
                'status'  => 'error',
                'message' => $verification['message'],
            ], 422);
        }

        // Deferred User Creation: Register or retrieve account ONLY after phone is verified
        $user = User::firstOrCreate(
            ['phone' => $phone],
            [
                'name'     => 'Guest (' . substr($phone, -4) . ')',
                'email'    => 'phone_' . preg_replace('/\D/', '', $phone) . '@justfeast.com',
                'role'     => 'customer',
                'password' => Hash::make(bin2hex(random_bytes(16))),
            ]
        );

        // Revoke prior tokens and issue fresh Sanctum token using User-Agent device name
        $user->tokens()->delete();
        $deviceName = $request->userAgent() ?: 'mobile-app';
        $token      = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Authentication successful.',
            'user'    => $user,
            'token'   => $token,
        ]);
    }
}

