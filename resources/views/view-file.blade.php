<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat File dari Dropbox</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-3xl">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">File dari Dropbox</h2>

        @if ($fileUrl)
            <div class="flex flex-col items-center space-y-4">
                @if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $fileUrl))
                    <!-- ✅ Jika file adalah gambar -->
                    <img src="{{ $fileUrl }}" alt="Gambar dari Dropbox" class="w-full rounded-lg shadow-md">
                @elseif (preg_match('/\.(pdf)$/i', $fileUrl))
                    <!-- ✅ Jika file adalah PDF -->
                    <iframe src="{{ $fileUrl }}" width="100%" height="600px" class="border"></iframe>
                @elseif (preg_match('/\.(mp4|webm|ogg)$/i', $fileUrl))
                    <!-- ✅ Jika file adalah video -->
                    <video controls class="w-full rounded-lg shadow-md">
                        <source src="{{ $fileUrl }}" type="video/mp4">
                        Browser Anda tidak mendukung pemutaran video.
                    </video>
                @elseif (preg_match('/\.(mp3|wav|ogg)$/i', $fileUrl))
                    <!-- ✅ Jika file adalah audio -->
                    <audio controls class="w-full">
                        <source src="{{ $fileUrl }}" type="audio/mpeg">
                        Browser Anda tidak mendukung pemutaran audio.
                    </audio>
                @else
                    <!-- ✅ Untuk file lain (Word, Excel, Zip, dll.), tampilkan link download -->
                    <p class="text-gray-600">Klik link di bawah untuk membuka file:</p>
                    <a href="{{ $fileUrl }}" target="_blank" class="text-blue-500 hover:text-blue-700 underline">
                        Lihat / Unduh File
                    </a>
                @endif
            </div>
        @else
            <p class="text-red-500">File tidak ditemukan.</p>
        @endif
    </div>
</body>
</html>