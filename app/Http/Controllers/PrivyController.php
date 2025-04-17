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
        $payload = $request->all();
        $result = $privy->registerUser($payload);

        return response()->json($result);
    }
}
