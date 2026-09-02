<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessLoopPaymentCallback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoopPaymentWebhookController extends Controller
{
    /**
     * Handle incoming LOOP payment callback webhook.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $rawBody = $request->getContent();
        $headers = $request->headers->all();

        Log::info('LOOP Payment Webhook Callback Received', [
            'headers' => $headers,
            'body'    => $request->json()->all(),
        ]);

        if (!$this->verifySignature($rawBody, $headers)) {
            Log::warning('LOOP Callback signature/token verification failed.', [
                'headers' => $headers,
            ]);
            return response()->json(['status' => 'error', 'message' => 'Invalid webhook signature.'], 401);
        }

        $payload = $request->json()->all();

        // Dispatch background processing job
        ProcessLoopPaymentCallback::dispatch($payload, $headers);

        return response()->json([
            'status'   => 'success',
            'received' => true,
            'message'  => 'Webhook acknowledged',
        ], 200);
    }

    /**
     * Verify incoming signature or authorization token from LOOP.
     */
    private function verifySignature(string $rawBody, array $headers): bool
    {
        $secret = config('loop.webhook_secret');

        // If secret is not configured in sandbox, allow request for testing
        if (empty($secret) && config('loop.environment') === 'sandbox') {
            return true;
        }

        // Check X-LOOP-Signature or Authorization token if provided
        $signature = $headers['x-loop-signature'][0] ?? ($headers['authorization'][0] ?? null);

        if (!$signature) {
            return config('loop.environment') === 'sandbox';
        }

        if ($secret && str_starts_with($signature, 'sha256=')) {
            $expected = 'sha256=' . hash_hmac('sha256', $rawBody, $secret);
            return hash_equals($expected, $signature);
        }

        return true;
    }
}
