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
    /**
     * 🔄 **Menampilkan halaman upload file ke Dropbox**
     */
    public function index(Request $request)
    {
        try {
            $accessToken = DropboxService::getAccessToken();

            if ($accessToken instanceof \Illuminate\Http\RedirectResponse) {
                return $accessToken;
            }

            $client = new Client($accessToken);
            $folderPath = '/uploads';
            $response = $client->listFolder($folderPath);
            Log::info("📂 [DROPBOX] Isi folder uploads:", $response['entries']);

            return view('dropbox-upload', [
                'files' => $response['entries']
            ]);
        } catch (Exception $e) {
            Log::error("🚨 [DROPBOX] Gagal mendapatkan daftar file!", ['error' => $e->getMessage()]);
            return view('dropbox-upload', [
                'files' => [],
                'error' => 'Gagal mendapatkan daftar file: ' . $e->getMessage()
            ]);
        }
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

            try {
                // 🔄 **Pastikan Access Token tersedia sebelum upload**
                $accessToken = DropboxService::getAccessToken();

                // **Jika `getAccessToken()` mengembalikan Redirect, hentikan eksekusi**
                if (filter_var($accessToken, FILTER_VALIDATE_URL)) {
                    return redirect($accessToken);
                }
                // Log::info("✅ [DROPBOX] Menggunakan Access Token untuk upload.");
            } catch (Exception $e) {
                // Log::warning("🚨 [DROPBOX] Access Token tidak tersedia. Redirecting ke OAuth...");
                return DropboxService::redirectToAuthorization();
            }

            // **🔍 Inisialisasi Client Spatie**
            $client = new Client($accessToken);

            // **📂 Ambil File dari Request**
            $file = $request->file('file');
            $fileName = '/' . $file->getClientOriginalName();
            // Log::info("📂 [DROPBOX] Upload file ke: " . $fileName);
            $filePath = '/uploads' . $fileName;
            $fileContent = file_get_contents($file->getRealPath()); // Baca isi file

            // Log::info("📂 [DROPBOX] Upload file ke: " . $filePath);

            // **🚀 Gunakan metode `upload()` dari Spatie Client**
            $response = $client->upload($filePath, $fileContent, 'add'); // 'add' mode agar tidak menimpa

            Log::info("✅ [DROPBOX] File berhasil diunggah: ", $response);

            return redirect()->route('dropbox.index')->with('success', 'File berhasil diunggah ke Dropbox!');
        } catch (Exception $e) {
            Log::error("🚨 [DROPBOX] Gagal mengunggah file!", ['error' => $e->getMessage()]);
            // ❌ **Jika gagal, redirect kembali ke halaman upload dengan pesan error**
            return redirect()->route('dropbox.index')->with('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }

    public function listFiles()
    {
        try {
            $accessToken = DropboxService::getAccessToken();

            // 🔄 **Pastikan Access Token tersedia**
            if ($accessToken instanceof \Illuminate\Http\RedirectResponse) {
                return $accessToken;
            }

            $client = new Client($accessToken);

            // 🔍 Ambil daftar file dalam folder "uploads"
            $folderPath = '/uploads';
            $response = $client->listFolder($folderPath);

            Log::info("📂 [DROPBOX] Isi folder uploads:", $response['entries']);

            return response()->json([
                'message' => 'Daftar file dalam Dropbox berhasil diambil',
                'files' => $response,
            ]);
        } catch (Exception $e) {
            Log::error("🚨 [DROPBOX] Gagal mendapatkan daftar file!", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal mendapatkan daftar file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 🔽 **Download File dari Dropbox**
     */
    /**
     * 📂 **Mengambil URL File dari Dropbox**
     */
    public function getFileUrl($filePath)
    {
        // Pastikan path sesuai dengan format Dropbox
        $filePath = '/' . ltrim($filePath, '/');
        $filePathLower = strtolower($filePath);

        try {
            Log::info("📂 [DROPBOX] Mengakses file: " . $filePath);

            $accessToken = DropboxService::getAccessToken();
            if ($accessToken instanceof \Illuminate\Http\RedirectResponse) {
                return $accessToken;
            }

            $client = new Client($accessToken);

            // 🔍 **Pastikan file ada di Dropbox sebelum mengambil link**
            $list = $client->listFolder('/uploads');

            $fileExists = collect($list['entries'])->firstWhere('path_lower', $filePathLower);

            if (!$fileExists) {
                Log::error("❌ [DROPBOX] File tidak ditemukan: " . $filePath);
                return null; // Kembalikan null jika file tidak ditemukan
            }

            // 🔍 **Ambil shared link yang sudah ada**
            Log::info("🔄 [DROPBOX] Mengecek shared links...");
            $sharedLinks = $client->listSharedLinks($filePath);
            $sharedLink = $sharedLinks[0]['url'] ?? null;
            if (!$sharedLink) {
                // ✅ Jika tidak ada shared link, buat satu
                try {
                    Log::info("⚡ [DROPBOX] Membuat shared link baru...");
                    $sharedLink = $client->createSharedLinkWithSettings($filePath)['url'];
                    Log::info("✅ [DROPBOX] Shared link baru: " . $sharedLink);
                } catch (\Exception $e) {
                    Log::error("❌ [DROPBOX] Gagal membuat shared link: " . $e->getMessage());
                    return null; // Kembalikan null jika gagal membuat shared link
                }
            }

            // 🔗 **Ubah link agar bisa langsung diakses**
            $fileUrl = str_replace('?dl=0', '?raw=1', $sharedLink);

            return $fileUrl; // Kembalikan fileUrl

        } catch (\Exception $e) {
            Log::error("❌ [DROPBOX] Gagal mendapatkan URL file: " . $e->getMessage());
            return null; // Kembalikan null jika terjadi error
        }
    }

    /**
     * 🔎 **Menampilkan File dari Dropbox**
     */
    public function viewFile($filePath)
    {
        try {
            Log::info("📂 [DROPBOX] Mengakses file untuk ditampilkan: " . $filePath);

            // 🔄 **Dapatkan URL file dari Dropbox**
            $fileUrl = $this->getFileUrl($filePath);
            // Cek apakah URL ditemukan
            if (!$fileUrl) {
                Log::error("❌ [DROPBOX] URL file tidak ditemukan untuk: " . $filePath);
                return abort(404, 'File tidak ditemukan di Dropbox.');
            }

            return redirect()->away($fileUrl);
        } catch (\Exception $e) {
            Log::error("❌ [DROPBOX] Gagal menampilkan file: " . $e->getMessage());
            return abort(404, 'Gagal menampilkan file dari Dropbox: ' . $e->getMessage());
        }
    }

    /**
     * ❌ **Hapus File dari Dropbox**
     */
    public function deleteFile($path)
    {
        try {
            // 🔄 **Dekode URL path yang telah diencode**
            $decodedPath = urldecode($path);

            Log::info("🗑 [DROPBOX] Menghapus file: " . $decodedPath);

            // 🔄 **Dapatkan Access Token**
            $accessToken = DropboxService::getAccessToken();

            if ($accessToken instanceof \Illuminate\Http\RedirectResponse) {
                return $accessToken;
            }

            // 🔍 **Inisialisasi Client Spatie**
            $client = new Client($accessToken);

            // ❌ **Hapus file dari Dropbox**
            $client->delete($decodedPath);

            Log::info("✅ [DROPBOX] File berhasil dihapus: " . $decodedPath);
            return redirect()->route('dropbox.index')->with('success', 'File berhasil dihapus dari Dropbox!');
        } catch (Exception $e) {
            Log::error("🚨 [DROPBOX] Gagal menghapus file!", ['error' => $e->getMessage()]);
            return redirect()->route('dropbox.index')->with('error', 'Gagal menghapus file: ' . $e->getMessage());
        }
    }
}
