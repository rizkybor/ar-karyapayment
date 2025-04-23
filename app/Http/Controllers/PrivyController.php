<?php

namespace App\Http\Controllers;

use App\Services\PrivyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

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

    public function uploadDocSignOnly(Request $request, PrivyService $privy)
    {
        // 1. Validasi request
        try {
            // 1. Validasi request
            $request->validate([
                'reference_number' => 'required|string',
                'channel_id' => 'required|string',
                'custom_signature_placement' => 'required|boolean',
                'doc_process' => 'required|in:0,1', // asumsinya hanya 0 atau 1
                'visibility' => 'required|boolean',

                // doc_owner
                'doc_owner' => 'required|array',
                'doc_owner.privyId' => 'required|string',
                'doc_owner.enterpriseToken' => 'required|string',

                // document
                'document' => 'required|array',
                'document.document_file' => 'required|string',
                'document.document_name' => 'required|string',
                'document.sign_process' => 'required|in:0,1',
                'document.barcode_position' => 'required|in:0,1',

                // recipients
                'recipients' => 'required|array|min:1',
                'recipients.*.detail' => 'required|in:true,false,"true","false"',
                'recipients.*.user_type' => 'required|in:0,1',
                'recipients.*.autosign' => 'required|in:0,1',
                'recipients.*.id_user' => 'required|string',
                'recipients.*.signer_type' => 'required|string',
                'recipients.*.enterpriseToken' => 'nullable|string',
                'recipients.*.sign_positions' => 'required|array|min:1',
                'recipients.*.sign_positions.*.posX' => 'required|string',
                'recipients.*.sign_positions.*.posY' => 'required|string',
                'recipients.*.sign_positions.*.signPage' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }

        $payload = $request->all();

        try {
            $response = $privy->uploadSignDocument($payload);
            return response()->json($response);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Gagal upload dokumen ke Privy',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadSignEMeterai(Request $request, PrivyService $privy)
    {
        $request->validate([
            'reference_number' => 'required|string',
            'channel_id' => 'required|string',
            'file' => 'required|file|mimes:pdf|max:5120',
            'doc_owner' => 'required|array',
            'document' => 'required|array',
            'recipients' => 'required|array|min:1',
        ]);

        $base64 = $privy->encodePdfToBase64($request->file('file')->getPathname());

        // Ambil payload dari request
        $payload = $request->except('file');

        // Tambahkan document_file ke dalam payload
        $payload['document']['document_file'] = $base64;

        return response()->json($privy->uploadSignDocument($payload));
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
