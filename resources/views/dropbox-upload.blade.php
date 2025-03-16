<div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
    <h2 class="text-xl font-semibold mb-4">📂 Upload File ke Dropbox</h2>

    {{-- ✅ Menampilkan pesan sukses/error --}}
    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
            🚨 {{ session('error') }}
        </div>
    @endif

    {{-- 📤 Form Upload --}}
    <div class="p-4 bg-gray-100 rounded-lg shadow-md mb-6">
        <form action="{{ route('dropbox.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label class="block text-gray-700 font-medium mb-2">Pilih File:</label>
            <input type="file" name="file" class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300" required>
            <button type="submit" class="mt-4 w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition">
                📤 Unggah ke Dropbox
            </button>
        </form>
    </div>

    {{-- 📁 Daftar File --}}
    <h3 class="text-lg font-semibold mb-3">📁 Daftar File di Dropbox</h3>

    @if (isset($files) && count($files) > 0)
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse border border-gray-300 shadow-md rounded-lg">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="p-3 border">Nama File</th>
                        <th class="p-3 border">Preview</th>
                        <th class="p-3 border">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($files as $file)
                        <tr class="border">
                            <td class="p-3 border">{{ $file['name'] }}</td>

                            {{-- 🖼 Preview File --}}
                            <td class="p-3 border text-center">
                                @php
                                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                                    $previewable = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'pdf']);
                                @endphp

                                @if ($previewable)
                                    <a href="{{ route('dropbox.file.view', ['filePath' => $file['path_lower']]) }}"
                                       target="_blank"
                                       class="text-blue-500 hover:underline">
                                        🖼 Lihat File
                                    </a>
                                @else
                                    <span class="text-gray-500">❌ Tidak Bisa Dilihat</span>
                                @endif
                            </td>

                            {{-- 🗑 Tombol Hapus --}}
                            <td class="p-3 border text-center">
                                <form action="{{ action([App\Http\Controllers\DropboxController::class, 'deleteFile'], ['path' => urlencode($file['path_lower'])]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus file ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                                        🗑 Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 text-center mt-4">📂 Tidak ada file di Dropbox.</p>
    @endif
</div>