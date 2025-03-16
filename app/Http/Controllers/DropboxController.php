<?php

namespace App\Http\Controllers;

use App\Services\DropboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class DropboxController extends Controller
{
    /**
     * 🔄 **Menampilkan halaman upload file ke Dropbox**
     */
    public function index(Request $request)
    {
        return view('dropbox-upload');
    }

    /**
     * 🔄 **Redirect ke halaman otorisasi Dropbox jika belum login**
     */
    public function redirectToAuthorization()
    {
        return DropboxService::redirectToAuthorization();
    }

    /**
     * 🔄 **Handle Callback setelah pengguna menyetujui akses Dropbox**
     */
    public function handleAuthorizationCallback(Request $request)
    {
        $authorizationCode = $request->query('code');

        if (!$authorizationCode) {
            return response()->json(['error' => 'Authorization Code tidak ditemukan'], 400);
        }

        // Tukarkan Authorization Code dengan Refresh Token
        DropboxService::handleAuthorizationCallback($request);

        return redirect()->route('dropbox.upload')->with('success', 'Dropbox berhasil dihubungkan!');
    }

    /**
     * 🔄 **Upload File ke Dropbox**
     */
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240', // Maksimal 10MB
            ]);

            // 🔄 **Pastikan Access Token tersedia sebelum upload**
            try {
                $accessToken = DropboxService::getAccessToken();

                // **Jika `getAccessToken()` mengembalikan Redirect, hentikan eksekusi**
                if (filter_var($accessToken, FILTER_VALIDATE_URL)) {
                    return redirect($accessToken);
                }

                Log::info("✅ [DROPBOX] Menggunakan Access Token untuk upload.");
            } catch (Exception $e) {
                Log::warning("🚨 [DROPBOX] Access Token tidak tersedia. Redirecting ke OAuth...");
                return DropboxService::redirectToAuthorization();
            }

            $file = $request->file('file');
            $filePath = 'uploads/' . $file->getClientOriginalName();

            Log::info("📂 [DROPBOX] Upload file: " . $filePath);
            Storage::disk('dropbox')->put($filePath, file_get_contents($file));

            // ✅ **Jika berhasil, redirect ke halaman upload dengan pesan sukses**
            return redirect()->route('dropbox.index')->with('success', 'File berhasil diunggah ke Dropbox!');
        } catch (Exception $e) {
            Log::error("🚨 [DROPBOX] Gagal mengunggah file!", ['error' => $e->getMessage()]);

            // ❌ **Jika gagal, redirect kembali ke halaman upload dengan pesan error**
            return redirect()->route('dropbox.index')->with('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }
}
