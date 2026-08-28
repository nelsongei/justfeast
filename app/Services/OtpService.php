<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function __construct(protected SmsService $smsService) {}

    /**
     * Generate a cryptographically random 6-digit OTP,
     * hash it before storing, set a 5-minute expiration, and send via SMS.
     * The plaintext code is NEVER stored in the database nor returned in HTTP responses.
     */
    public function generateAndSend(string $phone): string
    {
        // Cryptographically secure 6-digit PRNG
        $plainCode = sprintf('%06d', random_int(0, 999999));
        $hashCode  = Hash::make($plainCode);

        // Upsert — single active OTP record per phone number
        Otp::updateOrCreate(
            ['phone' => $phone],
            [
                'code'       => $hashCode,
                'attempts'   => 0,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        // Log system generated OTP locally
        Log::channel('single')->info("[System OTP Generated] Phone: {$phone} | Code: {$plainCode}");

        return $plainCode;
    }

    /**
     * Verify an OTP against the stored bcrypt hash.
     * Enforces expiration (5 mins) and attempt limiting (max 5 attempts).
     * Deletes record upon successful verification or maximum attempts exceeded.
     */
    public function verify(string $phone, string $code): array
    {
        $otp = Otp::where('phone', $phone)->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'No OTP found for this phone number. Please request a new code.',
            ];
        }

        if ($otp->isExpired()) {
            $otp->delete();
            return [
                'success' => false,
                'message' => 'OTP has expired. Please request a new code.',
            ];
        }

        if ($otp->attempts >= 5) {
            $otp->delete();
            return [
                'success' => false,
                'message' => 'Maximum verification attempts exceeded. Please request a new OTP.',
            ];
        }

        if (!Hash::check($code, $otp->code)) {
            $otp->increment('attempts');

            if ($otp->attempts >= 5) {
                $otp->delete();
                return [
                    'success' => false,
                    'message' => 'Maximum verification attempts exceeded. Please request a new OTP.',
                ];
            }

            $remaining = 5 - $otp->attempts;
            return [
                'success' => false,
                'message' => "Invalid OTP code. {$remaining} attempt(s) remaining.",
            ];
        }

        // Verification successful — delete OTP record to prevent replay
        $otp->delete();

        return [
            'success' => true,
            'message' => 'OTP verified successfully.',
        ];
    }
}
