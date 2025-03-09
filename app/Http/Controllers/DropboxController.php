<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\Dropbox\Client;


class DropboxController extends Controller
{
    public function index(Request $request)
    {
        return view('dropbox-upload');
    }
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Maksimal 10MB
        ]);

        $file = $request->file('file');
        $filePath = 'uploads/' . $file->getClientOriginalName();

        // Simpan file ke Dropbox
        Storage::disk('dropbox')->put($filePath, file_get_contents($file));

        return response()->json([
            'message' => 'File berhasil diunggah ke Dropbox!',
            'file_path' => $filePath,
        ]);
    }

    public function getFileUrl($filePath)
    {
        $filePath = '/uploads/' . ltrim($filePath, '/');

        try {
            Log::info("Mengakses file dari Dropbox: " . $filePath);

            $client = new Client(config('filesystems.disks.dropbox.access_token'));

            // 🔥 Log daftar file untuk debugging
            $list = $client->listFolder('/uploads');
            Log::info("Isi folder /uploads:", $list['entries']);

            // Cek apakah file memiliki shared link
            Log::info("Mengecek apakah file memiliki shared link: " . $filePath);
            $sharedLinks = $client->listSharedLinks($filePath);
            if (!empty($sharedLinks['links'])) {
                $sharedLink = $sharedLinks['links'][0]['url'];
                // dd($sharedLinks,'<<<< cek');
                Log::info("File sudah memiliki shared link: " . $sharedLink);
            } else {
                Log::info("File belum memiliki shared link, membuat baru...");
                $sharedLink = $client->createSharedLinkWithSettings($filePath)['url'];
                // dd($sharedLinks,'<<<< tes');

                Log::info("Shared link baru dibuat: " . $sharedLink);
            }

            // Konversi link agar bisa langsung diakses
            $fileUrl = str_replace('?dl=0', '?raw=1', $sharedLink);
            Log::info("URL file yang dikembalikan: " . $fileUrl);

            return response()->json([
                'message' => 'URL berhasil didapatkan',
                'file_url' => $fileUrl,
            ]);

        } catch (\Exception $e) {
            // 🔥 Logging error detail
            Log::error("Gagal mendapatkan URL file dari Dropbox: " . $e->getMessage());

            return response()->json([
                'error' => 'Gagal mendapatkan URL: ' . $e->getMessage(),
            ], 500);
        }
    }

    // public function getFileUrl($filePath)
    // {
    //     // Pastikan path benar & selalu dalam huruf kecil
    //     $filePath = '/uploads/' . ltrim($filePath, '/');
    //     $filePathLower = strtolower($filePath); // Pastikan lowercase

    //     try {
    //         Log::info("Mengakses file dari Dropbox: " . $filePath);

    //         $client = new \Spatie\Dropbox\Client(config('filesystems.disks.dropbox.access_token'));

    //         // 🔍 **Cek apakah file ada di Dropbox dengan path yang benar**
    //         $list = $client->listFolder('/uploads');
    //         Log::info("Isi folder /uploads:", $list['entries']);

    //         $fileExists = collect($list['entries'])->firstWhere('path_lower', $filePathLower);
    //         if (!$fileExists) {
    //             Log::error("File tidak ditemukan di Dropbox: " . $filePath);
    //             return response()->json([
    //                 'error' => 'File tidak ditemukan di Dropbox.',
    //             ], 404);
    //         }

    //         // 🔍 **Ambil semua shared links dari Dropbox**
    //         Log::info("Mengecek shared links yang sudah ada...");
    //         $allSharedLinks = $client->listSharedLinks();
    //         dd($allSharedLinks);
    //         // 🔎 **Cari apakah file ini sudah memiliki shared link**
    //         $sharedLink = collect($allSharedLinks['links'] ?? [])
    //             ->firstWhere('path_lower', $filePathLower)['url'] ?? null;

    //         if ($sharedLink) {
    //             Log::info("File sudah memiliki shared link: " . $sharedLink);
    //         } else {
    //             // 🔥 **Buat shared link baru hanya jika belum ada**
    //             Log::info("File belum memiliki shared link, membuat baru...");
    //             try {
    //                 $sharedLink = $client->createSharedLinkWithSettings($filePath)['url'];
    //                 Log::info("Shared link baru dibuat: " . $sharedLink);
    //             } catch (\Exception $e) {
    //                 Log::error("Gagal membuat shared link baru: " . $e->getMessage());
    //                 return response()->json([
    //                     'error' => 'Gagal membuat shared link baru: ' . $e->getMessage(),
    //                 ], 500);
    //             }
    //         }

    //         // 🔗 **Ubah link agar bisa langsung diakses**
    //         $fileUrl = str_replace('?dl=0', '?raw=1', $sharedLink);
    //         Log::info("URL file yang dikembalikan: " . $fileUrl);

    //         return response()->json([
    //             'message' => 'URL berhasil didapatkan',
    //             'file_url' => $fileUrl,
    //         ]);
    //     } catch (\Exception $e) {
    //         // 🚨 **Logging error detail**
    //         Log::error("Gagal mendapatkan URL file dari Dropbox: " . $e->getMessage());

    //         return response()->json([
    //             'error' => 'Gagal mendapatkan URL: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }


    public function viewFile($filePath)
    {
        try {
            Log::info("Mengakses file untuk ditampilkan: " . $filePath);

            // 🔥 Panggil `getFileUrl()` untuk mendapatkan link file dari Dropbox
            $response = $this->getFileUrl($filePath);

            // Cek apakah responsenya sukses
            if (!isset($response->original['file_url'])) {
                Log::error("Gagal mendapatkan file_url dari Dropbox untuk: " . $filePath);
                return abort(404, 'File tidak ditemukan di Dropbox.');
            }

            $fileUrl = $response->original['file_url']; // Ambil URL dari response

            Log::info("URL file yang dikembalikan untuk tampilan: " . $fileUrl);

            return view('view-file', compact('fileUrl'));
        } catch (\Exception $e) {
            Log::error("Gagal menampilkan file dari Dropbox: " . $e->getMessage());
            return abort(404, 'Gagal menampilkan file dari Dropbox: ' . $e->getMessage());
        }
    }

    public function listDropboxUploads()
    {
        try {
            $client = new Client(config('filesystems.disks.dropbox.access_token'));

            // Ambil daftar file di dalam folder 'uploads'
            $folderPath = '/uploads'; // Ubah sesuai kebutuhan
            $response = $client->listFolder($folderPath);

            Log::info("Isi folder /uploads di Dropbox:", $response['entries']);

            return response()->json([
                'message' => 'Daftar file dalam uploads berhasil diambil',
                'files' => $response['entries'],
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal mendapatkan daftar file dari Dropbox: " . $e->getMessage());

            return response()->json([
                'error' => 'Gagal mendapatkan daftar file: ' . $e->getMessage(),
            ], 500);
        }
    }
}
