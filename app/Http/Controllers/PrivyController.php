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
}
