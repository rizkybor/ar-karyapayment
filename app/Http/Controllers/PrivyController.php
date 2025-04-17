<?php

namespace App\Http\Controllers;

use App\Services\PrivyService;
use Illuminate\Http\JsonResponse;

class PrivyController extends Controller
{
    public function getToken(PrivyService $privyService): JsonResponse
    {
        $token = $privyService->getToken();

        return response()->json($token);
    }
}