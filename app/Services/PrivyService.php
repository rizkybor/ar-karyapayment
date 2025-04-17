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

            return [
                'message' => 'Mock register success (local env)',
                'data' => [
                    'reference_number' => $payload['reference_number'] ?? 'PRVID000000001',
                    'channel_id' => $payload['channel_id'] ?? '001',
                    'info' => $payload['info'] ?? 'Pendaftaran user mock',
                    'register_token' => Str::random(32),
                    'status' => 'waiting_verification'
                ]
            ];
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

    public function resendRegisterUser(array $payload): ?array
    {
        $timestamp = now('Asia/Jakarta')->format('Y-m-d\TH:i:sP');
        $requestId = Str::uuid()->toString();
        $apiKey = config('services.privy.api_key');
        $apiSecret = config('services.privy.secret_key');
        $httpVerb = 'POST';

        $rawJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $bodyMd5 = base64_encode(md5($rawJson, true));
        $signatureString = "{$timestamp}:{$apiKey}:{$httpVerb}:{$bodyMd5}";
        $hmacBase64 = base64_encode(hash_hmac('sha256', $signatureString, $apiSecret, true));

        $tokenData = $this->getToken();
        if (!$tokenData || !isset($tokenData['data']['access_token'])) {
            Log::error('Privy: Token tidak tersedia untuk resend register.');
            return ['error' => 'Access token unavailable'];
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Request-ID' => $requestId,
            'Timestamp' => $timestamp,
            'Signature' => $hmacBase64,
            'Authorization' => 'Bearer ' . $tokenData['data']['access_token'],
        ];

        $url = privy_base_url() . '/web/api/v2/register/resend';

        // Mock response untuk local
        if (config('app.env') === 'local') {
            Log::info('MOCK RESEND REGISTER:', [
                'headers' => $headers,
                'body' => $payload
            ]);

            return [
                'message' => 'Success retrieve data',
                'data' => [
                    'reference_number' => $payload['reference_number'],
                    'register_token' => $payload['register_token'],
                    'status' => 'waiting_verification',
                    'channel_id' => $payload['channel_id']
                ]
            ];
        }

        $response = Http::withHeaders($headers)->post($url, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Privy Resend Register Error', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return null;
    }

    public function checkRegisterStatus(array $payload): ?array
    {
        $timestamp = now('Asia/Jakarta')->format('Y-m-d\TH:i:sP');
        $requestId = Str::uuid()->toString();
        $apiKey = config('services.privy.api_key');
        $apiSecret = config('services.privy.secret_key');
        $httpVerb = 'POST';

        $rawJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $bodyMd5 = base64_encode(md5($rawJson, true));
        $signatureString = "{$timestamp}:{$apiKey}:{$httpVerb}:{$bodyMd5}";
        $hmacBase64 = base64_encode(hash_hmac('sha256', $signatureString, $apiSecret, true));

        $tokenData = $this->getToken();
        if (!$tokenData || !isset($tokenData['data']['access_token'])) {
            Log::error('Privy: Token tidak tersedia untuk cek status.');
            return ['error' => ['code' => 401, 'errors' => ['401 Unauthorized']]];
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Request-ID' => $requestId,
            'Timestamp' => $timestamp,
            'Signature' => $hmacBase64,
            'Authorization' => 'Bearer ' . $tokenData['data']['access_token'],
        ];

        $url = privy_base_url() . '/web/api/v2/register/status';

        // Mock response untuk local
        if (config('app.env') === 'local') {
            Log::info('MOCK CHECK REGISTER STATUS:', [
                'headers' => $headers,
                'body' => $payload
            ]);

            // Contoh response status verified (ganti dengan "rejected" bila ingin test lainnya)
            return [
                'message' => 'Success retrieve data',
                'data' => [
                    'reference_number' => $payload['reference_number'],
                    'channel_id' => $payload['channel_id'],
                    'info' => $payload['info'] ?? 'randomstring',
                    'register_token' => $payload['register_token'],
                    'status' => 'verified',
                    'privy_id' => 'DHIM0472',
                    'email' => 'dhimas.email@gmail.co',
                    'phone' => '62895630369573',
                    'identity' => [
                        'nama' => 'Dhimas Pramudya',
                        'nik' => '3302185203930001',
                        'tanggalLahir' => '1993-03-12',
                    ]
                ]
            ];

            // Contoh response jika ingin simulasikan rejected:
            /*
        return [
            'message' => 'Success retrieve data',
            'data' => [
                'reference_number' => $payload['reference_number'],
                'channel_id' => $payload['channel_id'],
                'register_token' => $payload['register_token'],
                'status' => 'rejected',
                'reject_reason' => [
                    'reason' => 'Nomor HP sudah terasosiasi dengan PrivyID lain',
                    'code' => 'RC09',
                ],
                'resend' => false
            ]
        ];
        */
        }

        $response = Http::withHeaders($headers)->post($url, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Privy Check Register Status Error', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [
            'error' => [
                'code' => $response->status(),
                'errors' => [json_decode($response->body(), true) ?? 'Unknown error']
            ]
        ];
    }
}
