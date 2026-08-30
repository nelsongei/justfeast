<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Venue;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * POST /api/auth/register-vendor
     * Register a new vendor user and business profile via API.
     */
    public function registerVendor(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'phone'         => 'nullable|string|max:20|unique:users,phone',
            'password'      => 'required|string|min:6',
            'event_id'      => 'nullable|exists:events,id',
            'logo_url'      => 'nullable|string|max:255',
        ]);

        $eventId = $this->getOrCreateActiveEventId($validated['event_id'] ?? null);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => 'vendor',
            'password' => Hash::make($validated['password']),
        ]);

        $vendor = Vendor::create([
            'user_id'       => $user->id,
            'business_name' => $validated['business_name'],
            'event_id'      => $eventId,
            'status'        => 'active',
            'logo_url'      => $validated['logo_url'] ?? null,
        ]);

        $deviceName = $request->userAgent() ?: 'mobile-app';
        $token      = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Vendor account registered successfully.',
            'user'    => $user->fresh(),
            'vendor'  => $vendor->load('event'),
            'token'   => $token,
        ], 201);
    }

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
            'mode'  => 'nullable|string|in:login,register',
        ]);

        $phone = trim($request->phone);
        $mode  = $request->input('mode', 'login');

        // Check if user account exists by phone
        $digitsOnly = preg_replace('/\D/', '', $phone);
        $user = User::where('phone', $phone)
            ->orWhere('phone', 'like', "%{$phone}")
            ->when(!empty($digitsOnly), function ($query) use ($digitsOnly) {
                $query->orWhereRaw("REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ' ', '') LIKE ?", ["%{$digitsOnly}"]);
            })
            ->first();

        // If in Log In mode and user does not exist, return explicit ACCOUNT_NOT_FOUND error
        if ($mode === 'login' && !$user) {
            return response()->json([
                'status'  => 'error',
                'code'    => 'ACCOUNT_NOT_FOUND',
                'message' => 'No account registered with phone number ' . $phone . '. Please click Register to create a new account.',
            ], 404);
        }

        // Generate cryptographic 6-digit system OTP
        $code = $this->otpService->generateAndSend($phone);

        return response()->json([
            'status'      => 'success',
            'message'     => $user ? 'System verification code generated.' : 'Verification code generated for registration.',
            'is_existing' => (bool) $user,
            'otp'         => $code,
            'phone'       => $phone,
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

    /** Ensure a valid active event ID exists */
    private function getOrCreateActiveEventId(?int $eventId = null): int
    {
        if ($eventId && Event::where('id', $eventId)->exists()) {
            return (int) $eventId;
        }

        $activeId = Event::where('status', 'active')->value('id');
        if ($activeId) {
            return (int) $activeId;
        }

        $firstId = Event::first()?->id;
        if ($firstId) {
            return (int) $firstId;
        }

        $venue = Venue::firstOrCreate(
            ['name' => 'Main Venue'],
            [
                'map_data'       => ['coordinates' => '0,0'],
                'seating_layout' => ['sections' => []],
            ]
        );

        $event = Event::create([
            'name'       => 'Main Concert Event',
            'venue_id'   => $venue->id,
            'start_time' => now(),
            'end_time'   => now()->addDays(30),
            'status'     => 'active',
        ]);

        return (int) $event->id;
    }
}

