<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Services\DropboxService;

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
     * 📂 **Get Single URL with fixed File from Dropbox (MODE TESTING)**
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
                return null;
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
                } catch (Exception $e) {
                    Log::error("❌ [DROPBOX] Gagal membuat shared link: " . $e->getMessage());
                    return null;
                }
            }

            // 🔗 **Ubah link agar bisa langsung diakses**
            $fileUrl = str_replace('?dl=0', '?raw=1', $sharedLink);

            return $fileUrl;
        } catch (Exception $e) {
            Log::error("❌ [DROPBOX] Gagal mendapatkan URL file: " . $e->getMessage());
            return null;
        }
    }

    public function downloadMultipleFromDropbox(array $paths, string $folder): array
    {
        try {
            $accessToken = DropboxService::getAccessToken();

            if ($accessToken instanceof \Illuminate\Http\RedirectResponse) {
                return [];
            }

            $client = new Client($accessToken);
            $localFiles = [];

            foreach ($paths as $dropboxPath) {
                $filename = basename($dropboxPath);
                $localTempPath = storage_path('app/temp_dl_' . uniqid() . '_' . $filename);

                // Ensure the path exists in Dropbox folder
                $list = $client->listFolder($folder);
                $fileExists = collect($list['entries'])->firstWhere('path_lower', strtolower($dropboxPath));
                if (!$fileExists) continue;

                $content = $client->download($dropboxPath);
                file_put_contents($localTempPath, $content);
                $localFiles[] = [
                    'path' => $localTempPath,
                    'name' => $filename
                ];
            }

            return $localFiles;
        } catch (Exception $e) {
            Log::error("[DROPBOX] Gagal download file: " . $e->getMessage());
            return [];
        }
    }


    /**
     * 🔄 **Upload File ke Dropbox fixed folder /uploads (MODE TESTING)**
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

    /**
     * 🔎 **Menampilkan List File dari Dropbox (MODE TESTING)**
     */
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
     * 🔎 **Menampilkan File dari Dropbox (MODE TESTING)**
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
        } catch (Exception $e) {
            Log::error("❌ [DROPBOX] Gagal menampilkan file: " . $e->getMessage());
            return abort(404, 'Gagal menampilkan file dari Dropbox: ' . $e->getMessage());
        }
    }

    /**
     * ❌ **Hapus File dari Dropbox (MODE TESTING)**
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

    /**
     * HANYA 5 FUNCTION DIBAWAH YANG DIGUNAKAN UNTUK APLIKASI AR : Redirect, Callback, View, Upload, Delete**
     */

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

        return redirect()->route('dropbox.index')->with('success', 'Dropbox berhasil dihubungkan!');
    }

    /**
     * 🔄 **Upload File ke Dropbox dynamic folder**
     */
    public function uploadAttachment($file, $fileName, $folderName)
    {
        try {
            // 🔄 **Pastikan Access Token tersedia sebelum upload**
            $accessToken = DropboxService::getAccessToken();

            if (filter_var($accessToken, FILTER_VALIDATE_URL)) {
                return redirect($accessToken);
            }

            // **🔍 Inisialisasi Client Spatie**
            $client = new Client($accessToken);

            // **🔍 Pastikan `file_name` tetap memiliki ekstensi**
            $originalExtension = $file->getClientOriginalExtension();
            $cleanFileName = strtolower(preg_replace('/[^A-Za-z0-9\-\_]/', '_', pathinfo($fileName, PATHINFO_FILENAME)));

            // **🎯 Format Unik: file_name + YYYYMMDDHHMMSS + uniqid() + random_bytes()**
            $uniqueId = now()->format('YmdHis') . '_' . uniqid('', true) . '_' . bin2hex(random_bytes(4));

            // **🔗 Gabungkan nama file & ID unik (hindari duplikasi `_`)**
            $finalFileName = trim("{$cleanFileName}_{$uniqueId}.{$originalExtension}", '_');

            // **📂 Tentukan path penyimpanan di Dropbox**
            $filePath = $folderName . $finalFileName;

            // 🚀 **Baca isi file dan unggah ke Dropbox**
            $fileContent = file_get_contents($file->getRealPath());
            $client->upload($filePath, $fileContent, 'add');

            // ✅ Kembalikan path dari Dropbox
            return $filePath;
        } catch (Exception $e) {
            Log::error("🚨 [DROPBOX] Gagal mengunggah file!", ['error' => $e->getMessage()]);
            return redirect()->route('dropbox.index')->with('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }

    /**
     * 📂 **Get Multi URL with dynamic folder File from Dropbox**
     */
    public function getAttachmentUrl($filePath, $folderName)
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
            // $list = $client->listFolder($folderName);
            // $fileExists = collect($list['entries'])->firstWhere('path_lower', $filePathLower);

            $list = $client->listFolder($folderName);
            $entries = $list['entries'];

            while ($list['has_more']) {
                $list = $client->listFolderContinue($list['cursor']);
                $entries = array_merge($entries, $list['entries']);
            }

            $fileExists = collect($entries)->firstWhere('path_lower', $filePathLower);

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
                } catch (Exception $e) {
                    Log::error("❌ [DROPBOX] Gagal membuat shared link: " . $e->getMessage());
                    return null;
                }
            }

            // 🔗 **Ubah link agar bisa langsung diakses**
            $fileUrl = str_replace('?dl=0', '?raw=1', $sharedLink);

            return $fileUrl;
        } catch (Exception $e) {
            Log::error("❌ [DROPBOX] Gagal mendapatkan URL file: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ❌ **Hapus File dari Dropbox**
     */
    public function deleteAttachment($path)
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
