<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Services\PrivyService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class PrivyController extends Controller
{
    public function getToken(PrivyService $privyService): JsonResponse
    {
        $token = $privyService->getToken();

        return response()->json($token);
    }

    public function getPrivySignaturePreview(Request $request, PrivyService $privy)
    {
        $payload = $request->all();

        $signatureData = $privy->generatePrivySignature($payload);

        return response()->json([
            'message' => 'Signature generated successfully',
            'signature_info' => $signatureData
        ]);
    }

    public function register(Request $request, PrivyService $privy)
    {
        // Ambil semua data dari body request (Postman raw JSON)
        $payload = $request->all();

        // Validasi dasar (opsional tapi disarankan)
        if (
            empty($payload['selfie']) ||
            empty($payload['identity']) ||
            empty($payload['email']) ||
            empty($payload['nik'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang dikirim tidak lengkap. Pastikan selfie, identity, email, dan nik terisi.',
            ], 422);
        }

        $result = $privy->registerUser($payload);

        return response()->json($result);
    }

    public function resendRegister(Request $request, PrivyService $privy)
    {
        $payload = $request->all();

        // Validasi dasar
        if (
            empty($payload['reference_number']) ||
            empty($payload['channel_id']) ||
            empty($payload['register_token'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter wajib tidak lengkap: reference_number, channel_id, register_token.',
            ], 422);
        }

        $result = $privy->resendRegisterUser($payload);

        return response()->json($result);
    }

    public function checkRegisterStatus(Request $request, PrivyService $privy)
    {
        $payload = $request->all();

        // Validasi minimum input
        if (
            empty($payload['reference_number']) ||
            empty($payload['channel_id']) ||
            empty($payload['register_token'])
        ) {
            return response()->json([
                'error' => [
                    'code' => 422,
                    'errors' => ['Data tidak ditemukan']
                ]
            ], 422);
        }

        $result = $privy->checkRegisterStatus($payload);

        return response()->json($result);
    }

    // Payload Privy Pattern 
    public function buildPrivyPayload($base64Pdf, $typeSign, $posX, $posY, $ref, $no)
    {
        // Generate reference_number: REFYYYYMMDDprimeXXXX
        $today = Carbon::now();
        $referenceNumber = $ref;

        $payload = [
            "reference_number" => $referenceNumber,
            "channel_id" => env('PRIVY_CHANNEL_ID'),
            "custom_signature_placement" => true,
            "doc_process" => $typeSign,
            "visibility" => true,
            "doc_owner" => [
                "privyId" => env('PRIVY_ID'),
                "enterpriseToken" => env('PRIVY_ENTERPRISE_TOKEN'),
            ],
            "document" => [
                "document_file" => "data:application/pdf;base64," . $base64Pdf,
                "document_name" => "PRIVY_" . $no,
                "sign_process" => "1",
                "barcode_position" => "1",
            ],
            "recipients" => [
                [
                    "detail" => "true",
                    "user_type" => "0",
                    "autosign" => "1",
                    "id_user" => env('PRIVY_ID_USER'),
                    "signer_type" => "Signer",
                    "enterpriseToken" => "",
                    "sign_positions" => [
                        [
                            "posX" => $posX,
                            "posY" => $posY,
                            "signPage" => "1",
                        ],
                        // [
                        //     "posX" => "200",
                        //     "posY" => "200",
                        //     "signPage" => "1",
                        // ],
                    ]
                ]
            ]
        ];

        return $payload;
    }

    public function generateDocument(Request $request, $privy)
    {
        try {
            // Validasi input minimal
            $request->validate([
                'base64_pdf' => 'required|string',
                'type_sign' => 'required|in:0,1',
                'posX' => 'required|string',
                'posY' => 'required|string',
                'doc_type' => 'required|string',
                'noSurat' => 'required|string'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }

        // Ambil input
        $base64Pdf = $request->base64_pdf;
        $typeSign = $request->type_sign;
        $posX = $request->posX;
        $posY = $request->posY;
        $ref = $request->doc_type;
        $no = $request->noSurat;

        // Bangun payload secara otomatis
        $payload = $this->buildPrivyPayload($base64Pdf,  $typeSign, $posX, $posY, $ref, $no);

        try {
            // Kirim ke Privy
            $response = $privy->uploadDocument($payload);

            Log::info('Privy uploadDocument response:', [
                'no_surat' => $no,
                'reference' => $ref,
                'response' => $response,
            ]);

            return response()->json($response);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Gagal upload dokumen ke Privy',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteDocument(Request $request, PrivyService $privy)
    {
        $payload = $request->only(['reference_number', 'document_token']);

        // Validasi dasar
        if (empty($payload['reference_number']) || empty($payload['document_token'])) {
            return response()->json([
                'error' => [
                    'code' => 422,
                    'errors' => ['Parameter reference_number dan document_token wajib diisi.']
                ]
            ], 422);
        }

        $result = $privy->deleteDocument($payload);

        return response()->json($result);
    }

    public function checkDocumentStatus(Request $request, PrivyService $privy)
    {
        $payload = $request->only(['reference_number', 'channel_id', 'document_token', 'info']);

        if (
            empty($payload['reference_number']) ||
            empty($payload['channel_id']) ||
            empty($payload['document_token'])
        ) {
            return response()->json([
                'error' => [
                    'code' => 422,
                    'errors' => ['reference_number, channel_id, dan document_token wajib diisi.']
                ]
            ], 422);
        }

        $result = $privy->checkDocumentStatus($payload);

        return response()->json($result);
    }

    public function checkDocumentHistory(Request $request, PrivyService $privy)
    {
        $payload = $request->only(['reference_number', 'channel_id', 'document_token', 'info']);

        // Validasi input wajib
        if (
            empty($payload['reference_number']) ||
            empty($payload['channel_id']) ||
            empty($payload['document_token'])
        ) {
            return response()->json([
                'error' => [
                    'code' => 422,
                    'errors' => ['reference_number, channel_id, dan document_token wajib diisi.']
                ]
            ], 422);
        }

        $result = $privy->checkDocumentHistory($payload);

        return response()->json($result);
    }

    public function requestOtp(Request $request, PrivyService $privy)
    {
        $payload = $request->only(['channel_id', 'signer_user_id', 'reference_numbers']);

        if (
            empty($payload['channel_id']) ||
            empty($payload['signer_user_id']) ||
            empty($payload['reference_numbers'])
        ) {
            return response()->json([
                'error' => [
                    'code' => 422,
                    'errors' => ['channel_id, signer_user_id, dan reference_numbers wajib diisi']
                ]
            ], 422);
        }

        $result = $privy->requestOtp($payload);

        return response()->json($result);
    }

    public function validateOtp(Request $request, PrivyService $privy)
    {
        $payload = $request->only(['otp_code', 'transaction_id']);

        if (empty($payload['otp_code']) || empty($payload['transaction_id'])) {
            return response()->json([
                'error' => [
                    'code' => 422,
                    'errors' => ['otp_code dan transaction_id wajib diisi']
                ]
            ], 422);
        }

        $result = $privy->validateOtp($payload);

        return response()->json($result);
    }
}
