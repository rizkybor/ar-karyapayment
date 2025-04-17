<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PrivyService
{
    public function getToken(): ?array
    {
        $url = privy_base_url() . '/oauth2/api/v1/token';

        // ✅ Gunakan Mock Response jika di lokal
        if (app()->environment('local')) {
            Http::fake([
                $url => Http::response([
                    'message' => 'Mocked token response',
                    'data' => [
                        'access_token' => 'mocked_token_1234567890',
                        'token_type' => 'Bearer',
                        'expires_in' => 3600,
                    ]
                ], 200)
            ]);
        }

        $response = Http::asJson()->post($url, [
            'client_id' => config('services.privy.username'),
            'client_secret' => config('services.privy.password'),
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        // Logging jika gagal
        Log::error('Privy Token Error', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        return null;
    }


    public function registerUser(array $payload): ?array
    {
        $timestamp = now('Asia/Jakarta')->format('Y-m-d\TH:i:sP');
        $requestId = Str::uuid()->toString();
        $apiKey = config('services.privy.api_key');
        $apiSecret = config('services.privy.secret_key');
        $httpVerb = 'POST';

        // Format body JSON dan hilangkan spasi (untuk signature)
        $rawJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $bodyMd5 = base64_encode(md5($rawJson, true));

        $signatureString = "{$timestamp}:{$apiKey}:{$httpVerb}:{$bodyMd5}";
        $hmac = hash_hmac('sha256', $signatureString, $apiSecret, true);
        $hmacBase64 = base64_encode($hmac);

        $tokenData = $this->getToken();
        if (!$tokenData || !isset($tokenData['data']['access_token'])) {
            Log::error('Privy: Token tidak tersedia atau tidak valid.');
            return ['error' => 'Token not available'];
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Request-ID' => $requestId,
            'Timestamp' => $timestamp,
            'Signature' => $hmacBase64,
            'Authorization' => 'Bearer ' . $this->getToken()['data']['access_token'],
        ];

        $url = privy_base_url() . '/web/api/v2/register';

        if (config('app.env') === 'local') {
            Log::info('MOCK REGISTER:', [
                'headers' => $headers,
                'body' => $payload
            ]);
            return ['message' => 'Mock register success (local env)'];
        }

        $response = Http::withHeaders($headers)->post($url, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Privy Register Error', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return null;
    }
}
