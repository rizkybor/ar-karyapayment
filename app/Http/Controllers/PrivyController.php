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
        // $payload = $request->all();

        $pathSelfie = public_path('images/selfie.jpg');
        $pathKTP = public_path('images/ktp.jpg');

        if (!file_exists($pathSelfie) || !file_exists($pathKTP)) {
            return response()->json([
                'success' => false,
                'message' => 'Gambar selfie atau KTP tidak ditemukan di server.',
            ], 400);
        }

        $payload = [
            "reference_number" => "PRVID" . str_pad(random_int(1, 999999999), 9, '0', STR_PAD_LEFT),
            "channel_id"       => "001",
            "info"             => "Pendaftaran user baru",
            "email"            => "user@example.com",
            "phone"            => "081234567890",
            "nik"              => "3302185203930001",
            "name"             => "John Doe",
            "dob"              => "1990-05-01",
            "selfie"           => 'data:image/jpeg;base64,' . base64_encode(file_get_contents(public_path('images/selfie.jpg'))),
            "identity"         => 'data:image/jpeg;base64,' . base64_encode(file_get_contents(public_path('images/ktp.jpg'))),
        ];

        $result = $privy->registerUser($payload);

        return response()->json($result);
    }
}
