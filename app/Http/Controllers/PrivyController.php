<?php

namespace App\Http\Controllers;

use App\Services\PrivyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrivyController extends Controller
{
    public function getToken(PrivyService $privyService): JsonResponse
    {
        $token = $privyService->getToken();

        return response()->json($token);
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
}
