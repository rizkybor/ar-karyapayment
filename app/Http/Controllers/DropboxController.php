<?php

namespace App\Http\Controllers;

use App\Services\DropboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\Dropbox\Client;
use Exception;


class DropboxController extends Controller
{
    public function index(Request $request)
    {
        return view('dropbox-upload');
    }
    public function redirectToAuthorization()
    {
        return DropboxService::redirectToAuthorization();
    }

    public function handleAuthorizationCallback(Request $request)
    {
        $authorizationCode = $request->query('code');

        if (!$authorizationCode) {
            return response()->json(['error' => 'Authorization Code tidak ditemukan'], 400);
        }

        DropboxService::exchangeAuthCodeForRefreshToken($authorizationCode);
        return redirect()->route('dropbox.upload')->with('success', 'Dropbox berhasil dihubungkan!');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

         // 🔄 **Pastikan Access Token tersedia sebelum upload**
         try {
            $accessToken = DropboxService::getAccessToken();
        } catch (Exception $e) {
            Log::warning("🚨 [DROPBOX] Access Token tidak tersedia. Redirecting ke OAuth...");
            return DropboxService::redirectToAuthorization();
        }
        $file = $request->file('file');
        $filePath = '/uploads/' . $file->getClientOriginalName();

        Storage::disk('dropbox')->put($filePath, file_get_contents($file));

        return response()->json([
            'message' => 'File berhasil diunggah ke Dropbox!',
            'file_path' => $filePath,
        ]);
    }
}
