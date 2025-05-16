<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrivyService
{
    public function getToken(): ?array
    {
        $url = privy_base_url() . '/oauth2/api/v1/token';

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

    public function uploadDocument(array $payload): ?array
    {
        // $timestamp = now('Asia/Jakarta')->format('Y-m-d\TH:i:sP');
        $timestamp = Carbon::now('Asia/Jakarta')->format('D M d Y H:i:s') . ' GMT+0700';
        $apiKey = config('services.privy.api_key');
        $apiSecret = config('services.privy.secret_key');
        $httpVerb = 'POST';

        $payloadForSignature = $payload;
        if (isset($payloadForSignature['document'])) {
            unset($payloadForSignature['document']);
        }

        $rawJson = json_encode($payloadForSignature, JSON_UNESCAPED_SLASHES);
        $rawJson = preg_replace('/\s+/', '', $rawJson);
        $bodyMd5 = base64_encode(md5($rawJson, true));
        $signatureString = "{$timestamp}:{$apiKey}:{$httpVerb}:{$bodyMd5}";
        $hmacBase64 = base64_encode(hash_hmac('sha256', $signatureString, $apiSecret, true));
        $finalSignature = base64_encode("{$apiKey}:{$hmacBase64}");

        // ✅ Ambil token
        $token = $this->getToken();
        if (!$token || !isset($token['data']['access_token'])) {
            Log::error('[Privy] Token tidak tersedia', ['token_response' => $token]);
            return [
                'error' => [
                    'code' => 401,
                    'errors' => ['Token tidak tersedia']
                ]
            ];
        }

        $headers = [
            'Timestamp' => $timestamp,
            'Signature' => $finalSignature,
            'Authorization' => $token['data']['token_type'] . ' ' . $token['data']['access_token'],
        ];

        $url = privy_base_url() . '/web/api/v2/doc-signing';
        // dd($originalPayload, $url);
        try {
            $response = Http::withHeaders($headers)->post($url, $payload);

            Log::info('Response API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                return [
                    'message' => 'Success retrieve data',
                    'data' => [
                        'reference_number'   => $responseData['data']['reference_number'] ?? null,
                        'channel_id'         => $responseData['data']['channel_id'] ?? null,
                        'document_token'     => $responseData['data']['document_token'] ?? null,
                        'status'             => $responseData['data']['status'] ?? null,
                        'message'            => $responseData['data']['message'] ?? null,
                        'unsigned_document'  => $responseData['data']['unsigned_document'] ?? null,
                        'signed_document'    => $responseData['data']['signed_document'] ?? null,
                    ],
                ];
            }

            return [
                'error' => [
                    'code' => $response->status(),
                    'errors' => [json_decode($response->body(), true) ?? 'Unknown error']
                ]
            ];
        } catch (\Throwable $e) {
            Log::error('[Privy] Exception saat upload dokumen', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => [
                    'code' => 500,
                    'errors' => ['Exception: ' . $e->getMessage()]
                ]
            ];
        }
    }

    public function checkDocSigningStatus(array $payload): ?array
    {
        $timestamp = now('Asia/Jakarta')->format('D M d Y H:i:s') . ' GMT+0700';
        $apiKey = config('services.privy.api_key');
        $apiSecret = config('services.privy.secret_key');
        $httpVerb = 'POST';

        // Format JSON tanpa spasi
        $rawJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $rawJson = preg_replace('/\s+/', '', $rawJson);
        $bodyMd5 = base64_encode(md5($rawJson, true));

        $signatureString = "{$timestamp}:{$apiKey}:{$httpVerb}:{$bodyMd5}";
        $hmacBase64 = base64_encode(hash_hmac('sha256', $signatureString, $apiSecret, true));
        $finalSignature = base64_encode("{$apiKey}:{$hmacBase64}");

        // Ambil token akses
        $tokenData = $this->getToken();
        if (!$tokenData || !isset($tokenData['data']['access_token'])) {
            Log::error('Privy: Token tidak tersedia untuk doc-signing status.');
            return [
                'error' => [
                    'code' => 401,
                    'errors' => ['Token tidak tersedia']
                ]
            ];
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Timestamp' => $timestamp,
            'Signature' => $finalSignature,
            'Authorization' => $tokenData['data']['token_type'] . ' ' . $tokenData['data']['access_token'],
        ];

        $url = privy_base_url() . '/web/api/v2/doc-signing/status';

        $response = Http::withHeaders($headers)->post($url, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Privy Doc-Signing Status Error', [
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

    public function deleteDocument(array $payload): ?array
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
            Log::error('Privy: Token tidak tersedia untuk delete document.');
            return [
                'error' => [
                    'code' => 401,
                    'errors' => ['Token tidak tersedia']
                ]
            ];
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Request-ID' => $requestId,
            'Timestamp' => $timestamp,
            'Signature' => $hmacBase64,
            'Authorization' => 'Bearer ' . $tokenData['data']['access_token'],
        ];

        $url = privy_base_url() . '/web/api/v2/doc-signing/delete';

        // Return mock jika local
        if (config('app.env') === 'local') {
            Log::info('MOCK DELETE DOCUMENT:', [
                'headers' => $headers,
                'body' => $payload
            ]);

            return [
                'message' => 'has been deleted',
                'data' => [
                    'document_token' => $payload['document_token'],
                    'deleted_at' => now()->toIso8601String()
                ]
            ];
        }

        $response = Http::withHeaders($headers)->post($url, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Privy Delete Document Error', [
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
