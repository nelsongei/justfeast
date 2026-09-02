<?php

namespace App\Services\Loop;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class LoopTokenProvider
{
    /**
     * Retrieve or generate a cached OAuth access token for LOOP API.
     */
    public function token(): string
    {
        $cacheKey = 'loop:oauth-token:' . config('loop.environment');

        return Cache::remember($cacheKey, now()->addMinutes(50), function (): string {
            $tokenUrl     = config('loop.token_url');
            $clientId     = config('loop.client_id');
            $clientSecret = config('loop.client_secret');

            if (empty($tokenUrl)) {
                throw new RuntimeException('LOOP_TOKEN_URL is not configured.');
            }

            try {
                $request = Http::asForm()
                    ->connectTimeout(config('loop.connect_timeout'))
                    ->timeout(config('loop.request_timeout'));

                if (!empty($clientId) && !empty($clientSecret)) {
                    $request->withBasicAuth($clientId, $clientSecret);
                }

                $response = $request->post($tokenUrl, [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                ]);

                Log::info('LOOP token response', [
                    'http_status'       => $response->status(),
                    'has_access_token'  => filled($response->json('access_token')),
                    'token_type'        => $response->json('token_type'),
                    'expires_in'        => $response->json('expires_in'),
                    'error'             => $response->json('error'),
                    'error_description' => $response->json('error_description'),
                ]);

                if ($response->successful()) {
                    $token = $response->json('access_token');
                    if (is_string($token) && $token !== '') {
                        return $token;
                    }
                }

                throw new RuntimeException('LOOP token response did not contain a valid access token: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('LOOP token generation exception: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Evict cached token upon authentication failure (HTTP 401).
     */
    public function forget(): void
    {
        $cacheKey = 'loop:oauth-token:' . config('loop.environment');
        Cache::forget($cacheKey);
        Cache::forget('loop:access-token');
    }
}
