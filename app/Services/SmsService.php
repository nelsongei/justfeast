<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS containing the OTP message to the specified phone number.
     * The code is delivered out-of-band and never returned in API payloads.
     */
    public function sendOtp(string $phone, string $code): bool
    {
        $driver = config('services.sms.driver', env('SMS_DRIVER', 'log'));
        $message = "Your JustFeast verification code is: {$code}. Valid for 5 minutes. Do not share this code.";

        if ($driver === 'africastalking') {
            return $this->sendViaAfricasTalking($phone, $message);
        }

        if ($driver === 'twilio') {
            return $this->sendViaTwilio($phone, $message);
        }

        // Default / Local / Staging fallback: single log channel
        Log::channel('single')->info("[SMS] Phone: {$phone} | Message: {$message} | Timestamp: " . now()->toDateTimeString());
        return true;
    }

    protected function sendViaAfricasTalking(string $phone, string $message): bool
    {
        $username = config('services.africastalking.username', env('AFRICASTALKING_USERNAME'));
        $apiKey   = config('services.africastalking.api_key', env('AFRICASTALKING_API_KEY'));

        if (!$username || !$apiKey) {
            Log::channel('single')->warning("[SMS Provider Config Missing] Fallback Log: Phone: {$phone} | Code Message: {$message}");
            return true;
        }

        try {
            $response = Http::withHeaders([
                'apiKey' => $apiKey,
                'Accept' => 'application/json',
            ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                'username' => $username,
                'to'       => $phone,
                'message'  => $message,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("[SMS Send Exception] " . $e->getMessage());
            return false;
        }
    }

    protected function sendViaTwilio(string $phone, string $message): bool
    {
        $sid   = config('services.twilio.sid', env('TWILIO_SID'));
        $token = config('services.twilio.auth_token', env('TWILIO_AUTH_TOKEN'));
        $from  = config('services.twilio.from', env('TWILIO_FROM'));

        if (!$sid || !$token || !$from) {
            Log::channel('single')->warning("[SMS Provider Config Missing] Fallback Log: Phone: {$phone} | Code Message: {$message}");
            return true;
        }

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'To'   => $phone,
                    'From' => $from,
                    'Body' => $message,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("[Twilio SMS Exception] " . $e->getMessage());
            return false;
        }
    }
}
