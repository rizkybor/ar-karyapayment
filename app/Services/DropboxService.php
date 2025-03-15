<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Exception;

class DropboxService
{
    /**
     * 🔄 **Redirect pengguna ke halaman otorisasi Dropbox**
     */
    public static function redirectToAuthorization()
    {
        $clientId = env('DROPBOX_APP_KEY');
        $redirectUri = env('DROPBOX_REDIRECT_URI');

        if (!$clientId || !$redirectUri) {
            throw new Exception("🚨 Konfigurasi Dropbox tidak ditemukan di .env");
        }

        Session::put('previous_url', url()->previous());

        $authUrl = "https://www.dropbox.com/oauth2/authorize?client_id={$clientId}&response_type=code&token_access_type=offline&redirect_uri={$redirectUri}";

        Log::info("🔗 [DROPBOX] Redirecting user to: " . $authUrl);
        return redirect()->away($authUrl);
    }

    /**
     * 🔄 **Menukar Authorization Code menjadi Refresh Token**
     */
    public static function exchangeAuthCodeForRefreshToken($authorizationCode)
    {
        Log::info("🔄 [DROPBOX] Menukar Authorization Code dengan Refresh Token...");

        try {
            $response = Http::asForm()->post('https://api.dropboxapi.com/oauth2/token', [
                'grant_type' => 'authorization_code',
                'code' => $authorizationCode,
                'client_id' => env('DROPBOX_APP_KEY'),
                'client_secret' => env('DROPBOX_APP_SECRET'),
                'redirect_uri' => env('DROPBOX_REDIRECT_URI'),
            ]);

            if ($response->failed()) {
                throw new Exception("🚨 Gagal mendapatkan Refresh Token: " . json_encode($response->json()));
            }

            $refreshToken = $response->json()['refresh_token'];

            self::saveToEnv('DROPBOX_REFRESH_TOKEN', $refreshToken);
            Log::info("✅ [DROPBOX] Refresh Token berhasil diperoleh dan disimpan.");

            return $refreshToken;
        } catch (Exception $e) {
            Log::error("🚨 [DROPBOX] Error mendapatkan Refresh Token", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * 🔄 **Mendapatkan Access Token menggunakan Refresh Token**
     */
    public static function getAccessToken()
    {
        if (Cache::has('dropbox_access_token')) {
            return Cache::get('dropbox_access_token');
        }

        $refreshToken = env('DROPBOX_REFRESH_TOKEN');
        if (!$refreshToken) {
            return self::redirectToAuthorization();
        }

        $response = Http::asForm()->post('https://api.dropboxapi.com/oauth2/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => env('DROPBOX_APP_KEY'),
            'client_secret' => env('DROPBOX_APP_SECRET'),
        ]);

        if ($response->failed()) {
            return self::redirectToAuthorization();
        }

        $accessToken = $response->json()['access_token'];
        Cache::put('dropbox_access_token', $accessToken, now()->addHours(3));

        return $accessToken;
    }

    /**
     * 🔄 **Simpan nilai ke `.env`**
     */
    private static function saveToEnv($key, $value)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        if (strpos($envContent, "{$key}=") !== false) {
            $envContent = preg_replace("/{$key}=.*/", "{$key}={$value}", $envContent);
        } else {
            $envContent .= "\n{$key}={$value}\n";
        }

        file_put_contents($envFile, $envContent);
        Log::info("✅ [DROPBOX] {$key} berhasil diperbarui di .env");
    }
}