<div class="container">
    <h2 class="mb-3">📂 Upload File ke Dropbox</h2>

    {{-- Menampilkan pesan sukses/error --}}
    @if (session()->has('success'))
        <div class="alert alert-success" role="alert">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger" role="alert">
            🚨 {{ session('error') }}
        </div>
    @endif

    {{-- Form Upload --}}
    <div class="card p-3 mb-4">
        <form action="{{ route('dropbox.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <input type="file" name="file" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">📤 Unggah ke Dropbox</button>
        </form>
    </div>

    <h3>📁 Daftar File di Dropbox</h3>

    {{-- Tampilkan daftar file jika ada --}}
    @if (isset($files) && count($files) > 0)
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nama File</th>
                    <th>Preview</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($files as $file)
                    <tr>
                        <td>{{ $file['name'] }}</td>

                        {{-- Preview File --}}
                        <td>
                            @php
                                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                                $previewable = in_array(strtolower($extension), [
                                    'jpg',
                                    'jpeg',
                                    'png',
                                    'gif',
                                    'webp',
                                    'mp4',
                                    'webm',
                                    'pdf',
                                ]);
                            @endphp

                            @if ($previewable)
                                <a href="{{ route('dropbox.file.view', ['filePath' => $file['path_lower']]) }}"
                                    target="_blank">
                                    🖼 Lihat File
                                </a>
                            @else
                                <span class="text-muted">❌ Tidak Bisa Dilihat</span>
                            @endif
                        </td>

                        {{-- Tombol Aksi --}}
                        <td>

                            {{-- Tombol Hapus --}}
                            <form
                                action="{{ action([App\Http\Controllers\DropboxController::class, 'deleteFile'], ['path' => urlencode($file['path_lower'])]) }}"
                                method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">🗑 Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">📂 Tidak ada file di Dropbox.</p>
    @endif
</div>
