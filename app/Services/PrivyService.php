<?php

namespace App\Services;

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

        Log::error('Privy Token Error', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        return null;
    }
}