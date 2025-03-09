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
        // Pastikan path sesuai dengan format Dropbox
        $filePath = '/uploads/' . ltrim($filePath, '/');
        $filePathLower = strtolower($filePath);
    
        try {
            Log::info("Mengakses file dari Dropbox: " . $filePath);
    
            $client = new \Spatie\Dropbox\Client(config('filesystems.disks.dropbox.access_token'));
    
            // 🔍 **Pastikan file ada di Dropbox sebelum mengambil link**
            $list = $client->listFolder('/uploads');
            Log::info("Isi folder /uploads:", $list['entries']);
    
            $fileExists = collect($list['entries'])->firstWhere('path_lower', $filePathLower);
            if (!$fileExists) {
                Log::error("File tidak ditemukan di Dropbox: " . $filePath);
                return null; // Kembalikan null jika file tidak ditemukan
            }
    
            // 🔍 **Ambil shared link yang sudah ada**
            Log::info("Mengecek shared links yang sudah ada...");
            $sharedLinks = $client->listSharedLinks($filePath);
            $sharedLink = $sharedLinks[0]['url'] ?? null;
           
            if ($sharedLink) {
                Log::info("Menggunakan shared link yang sudah ada: " . $sharedLink);
            } else {
                // ✅ Jika tidak ada shared link, buat satu kali saja
                try {
                    Log::info("Membuat shared link baru...");
                    $sharedLink = $client->createSharedLinkWithSettings($filePath)['url'];
                    Log::info("Shared link baru dibuat: " . $sharedLink);
                } catch (\Exception $e) {
                    Log::error("Gagal membuat shared link: " . $e->getMessage());
                    return null; // Kembalikan null jika gagal membuat shared link
                }
            }
    
            // 🔗 **Ubah link agar bisa langsung diakses**
            $fileUrl = str_replace('?dl=0', '?raw=1', $sharedLink);
            Log::info("URL file yang dikembalikan: " . $fileUrl);
    
            return $fileUrl; // Kembalikan fileUrl
    
        } catch (\Exception $e) {
            Log::error("Gagal mendapatkan URL file dari Dropbox: " . $e->getMessage());
    
            return null; // Kembalikan null jika terjadi error
        }
    }


    public function viewFile($filePath)
    {
        try {
            Log::info("Mengakses file untuk ditampilkan: " . $filePath);
    
            // 🔥 Panggil `getFileUrl()` untuk mendapatkan link file dari Dropbox
            $fileUrl = $this->getFileUrl($filePath);
    
            // Cek apakah URL file ditemukan
            if (!$fileUrl) {
                Log::error("Gagal mendapatkan file_url dari Dropbox untuk: " . $filePath);
                return abort(404, 'File tidak ditemukan di Dropbox.');
            }
    
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
            dd($response);
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
