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

        Log::info('URL:', [
            'headers' => $url
        ]);

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


    public function generatePrivySignature(array $payload): array
    {
        $timestamp = now('Asia/Jakarta')->format('Y-m-d\TH:i:sP');
        $requestId = \Illuminate\Support\Str::uuid()->toString();
        $apiKey = config('services.privy.api_key');
        $apiSecret = config('services.privy.secret_key');
        $httpVerb = 'POST';
    
        
        $excludedKeys = ['ktp', 'identity', 'selfie', 'supporting_docs', 'document'];
        $bodyForSignature = collect($payload)->except($excludedKeys)->all();
    
        $rawJson = json_encode($bodyForSignature, JSON_UNESCAPED_SLASHES);
        $rawJson = str_replace(' ', '', $rawJson); // sesuai dokumentasi
    
        $bodyMd5 = base64_encode(md5($rawJson, true));
    
        $signatureString = "{$timestamp}:{$apiKey}:{$httpVerb}:{$bodyMd5}";
    
        $hmacBase64 = base64_encode(hash_hmac('sha256', $signatureString, $apiSecret, true));
    
        $authString = "{$apiKey}:{$hmacBase64}";
        $finalSignature = base64_encode($authString);
    
        return [
            'headers' => [
                'Content-Type' => 'application/json',
                'Request-ID' => $requestId,
                'Timestamp' => $timestamp,
                'Signature' => $finalSignature,
            ],
    
            // Debugging (optional)
            'debug' => [
                'timestamp' => $timestamp,
                'request_id' => $requestId,
                'api_key' => $apiKey,
                'signature_string' => $signatureString,
                'hmac_base64' => $hmacBase64,
                'auth_string' => $authString,
                'final_signature' => $finalSignature,
                'body_md5' => $bodyMd5,
                'raw_json' => $rawJson,
            ]
        ];
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

    public function encodePdfToBase64(string $path): string
    {
        return 'data:application/pdf;base64,' . base64_encode(file_get_contents($path));
    }

    public function uploadSignDocument(array $payload): ?array
    {

        $timestamp = now('Asia/Jakarta')->format('Y-m-d\TH:i:sP');
        $requestId = Str::uuid()->toString();
        $apiKey = config('services.privy.api_key');
        $apiSecret = config('services.privy.secret_key');
        $httpVerb = 'POST';

        // ✅ Decode json jika masih string
        foreach (['doc_owner', 'document', 'recipients'] as $key) {
            if (isset($payload[$key]) && is_string($payload[$key])) {
                $decoded = json_decode($payload[$key], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $payload[$key] = $decoded;
                } else {
                    return [
                        'error' => [
                            'code' => 400,
                            'errors' => ["Payload '{$key}' harus berupa objek JSON yang valid."]
                        ]
                    ];
                }
            }
        }

        // ✅ Signature building
        $excludedKeys = ['ktp', 'identity', 'selfie', 'supporting_docs', 'document'];
        $bodyForSignature = collect($payload)->except($excludedKeys)->all();

        // Encode JSON dan hapus spasi sesuai dokumen (ganti spasi jadi kosong)
        $rawJson = json_encode($bodyForSignature, JSON_UNESCAPED_SLASHES);
        $rawJson = str_replace(' ', '', $rawJson); // <- sesuai petunjuk dokumen

        // Signature building
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

        // ✅ Headers TANPA set manual Content-Type
        $headers = [
            'Request-ID' => $requestId,
            'Timestamp' => $timestamp,
            'Signature' => $finalSignature,
            'Authorization' => 'Bearer ' . $token['data']['access_token'],
        ];

        $url = privy_base_url() . '/web/api/v2/doc-signing';

        Log::info('[Privy] Mengirim dokumen ke Privy API', [
            'url' => $url,
            'headers' => $headers,
            'payload' => $payload,
            'rawJson' => $rawJson,
        ]);

        // ✅ Return MOCK di local
        if (app()->environment('local')) {
            return [
                'message' => 'Mocked document upload success',
                'data' => [
                    'reference_number' => $payload['reference_number'] ?? 'MOCK123',
                    'channel_id' => $payload['channel_id'] ?? 'TEST',
                    'document_token' => Str::random(32),
                    'status' => 'uploaded',
                    'signing_url' => 'https://dev.dcidi.io/mock-sign-url'
                ]
            ];
        }

        try {
            $response = Http::withHeaders($headers)->post($url, $payload);

            if ($response->successful()) {
                Log::info('[Privy] Upload berhasil', ['response' => $response->json()]);
                return $response->json();
            }

            Log::error('[Privy] Upload gagal', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

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

    public function checkDocumentStatus(array $payload): ?array
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

        $token = $this->getToken();
        if (!$token || !isset($token['data']['access_token'])) {
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
            'Authorization' => 'Bearer ' . $token['data']['access_token'],
        ];

        $url = privy_base_url() . '/web/api/v2/doc-signing/status';

        // MOCK Response
        if (config('app.env') === 'local') {
            return [
                'message' => 'Success retrieve data',
                'data' => [
                    'reference_number' => $payload['reference_number'],
                    'channel_id' => $payload['channel_id'],
                    'status' => 'uploaded',
                    'document_token' => $payload['document_token'],
                    'signing_url' => 'https://dev.dcidi.io/531c398559',
                    'signed_document' => null
                ]
            ];
        }

        $response = Http::withHeaders($headers)->post($url, $payload);

        return $response->successful()
            ? $response->json()
            : [
                'error' => [
                    'code' => $response->status(),
                    'errors' => [json_decode($response->body(), true) ?? 'Unknown error']
                ]
            ];
    }

    public function checkDocumentHistory(array $payload): ?array
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

        $token = $this->getToken();
        if (!$token || !isset($token['data']['access_token'])) {
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
            'Authorization' => 'Bearer ' . $token['data']['access_token'],
        ];

        $url = privy_base_url() . '/web/api/v2/doc-signing/history';

        if (config('app.env') === 'local') {
            return [
                'message' => 'Success retrieve data',
                'data' => [
                    'document' => [
                        [
                            'name' => '17442_OPR_PETANI_XII_2024.pdf',
                            'status' => 'blocked',
                            'document_token' => $payload['document_token'],
                            'reference_number' => $payload['reference_number'],
                            'signers' => [
                                [
                                    'id' => 123080,
                                    'privy_id' => 'aii6564',
                                    'name' => 'Anisa Nurfadila Dwi Karina',
                                    'signer_type' => 'signer',
                                    'recipient_type' => 'signer',
                                    'status' => 'blocked',
                                    'stamp_status' => null,
                                    'histories' => [
                                        [
                                            'description' => 'link opened',
                                            'created_at' => now()->toIso8601String()
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

        $response = Http::withHeaders($headers)->post($url, $payload);

        return $response->successful()
            ? $response->json()
            : ['error' => ['code' => $response->status(), 'errors' => [json_decode($response->body(), true)]]];
    }

    public function requestOtp(array $payload): ?array
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

        $token = $this->getToken();
        if (!$token || !isset($token['data']['access_token'])) {
            return ['error' => ['code' => 401, 'errors' => ['Token tidak tersedia']]];
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Request-ID' => $requestId,
            'Timestamp' => $timestamp,
            'Signature' => $hmacBase64,
            'Authorization' => 'Bearer ' . $token['data']['access_token'],
        ];

        $url = privy_base_url() . '/web/api/v2/doc-signing/otp/request';

        if (config('app.env') === 'local') {
            return [
                'message' => 'Kode OTP akan dikirim ke nomor handphone',
                'data' => [
                    'transaction_id' => Str::uuid()->toString(),
                    'is_validate' => false,
                    'otp_code' => '15079',
                    'validation_count' => 0,
                    'maximum_otp_request' => 3,
                    'maximum_otp_validation' => 1000,
                    'otp_reset_validation' => false,
                    'request_count' => 1
                ]
            ];
        }

        $response = Http::withHeaders($headers)->post($url, $payload);

        return $response->successful()
            ? $response->json()
            : ['error' => ['code' => $response->status(), 'errors' => [json_decode($response->body(), true)]]];
    }

    public function validateOtp(array $payload): ?array
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

        $token = $this->getToken();
        if (!$token || !isset($token['data']['access_token'])) {
            return ['error' => ['code' => 401, 'errors' => ['Token tidak tersedia']]];
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Request-ID' => $requestId,
            'Timestamp' => $timestamp,
            'Signature' => $hmacBase64,
            'Authorization' => 'Bearer ' . $token['data']['access_token'],
        ];

        $url = privy_base_url() . '/web/api/v2/doc-signing/otp/validation';

        if (config('app.env') === 'local') {
            return [
                'message' => 'Terima Kasih. Proses Penandatanganan Elektronik Dokumen Telah Berhasil.',
                'data' => true
            ];
        }

        $response = Http::withHeaders($headers)->post($url, $payload);

        return $response->successful()
            ? $response->json()
            : ['error' => ['code' => $response->status(), 'errors' => [json_decode($response->body(), true)]]];
    }
}
