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
     * Accept phone number, optional name, and optional email.
     * Generates a system OTP and returns it directly in response (SMS bypass).
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:9|max:20',
            'name'  => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
        ]);

        $phone = $request->phone;

        // Generate cryptographic 6-digit system OTP
        $code = $this->otpService->generateAndSend($phone);

        return response()->json([
            'status'  => 'success',
            'message' => 'System verification code generated.',
            'otp'     => $code,
            'phone'   => $phone,
        ]);
    }

    /**
     * POST /api/auth/verify
     * Verify the OTP code. On successful verification, register/update the User account
     * with name, phone, and email, then issue a Sanctum access token.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string|size:6',
            'name'  => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
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

        // Register or retrieve user account after verification
        $user = User::where('phone', $phone)->first();

        $defaultName  = $request->filled('name') ? trim($request->name) : 'Customer (' . substr($phone, -4) . ')';
        $defaultEmail = $request->filled('email') ? trim($request->email) : 'customer_' . preg_replace('/\D/', '', $phone) . '@justfeast.co.ke';

        if (!$user) {
            $user = User::create([
                'phone'    => $phone,
                'name'     => $defaultName,
                'email'    => $defaultEmail,
                'role'     => 'customer',
                'password' => Hash::make(bin2hex(random_bytes(16))),
            ]);
        } else {
            // Update name and email if provided during login/registration
            $updates = [];
            if ($request->filled('name')) {
                $updates['name'] = trim($request->name);
            }
            if ($request->filled('email')) {
                $updates['email'] = trim($request->email);
            }
            if (!empty($updates)) {
                $user->update($updates);
            }
        }

        // Revoke prior tokens and issue fresh Sanctum token using User-Agent device name
        $user->tokens()->delete();
        $deviceName = $request->userAgent() ?: 'mobile-app';
        $token      = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Authentication successful.',
            'user'    => $user->fresh(),
            'token'   => $token,
        ]);
    }
}

