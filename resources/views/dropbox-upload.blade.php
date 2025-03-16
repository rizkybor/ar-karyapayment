@if(session()->has('success'))
    <div style="color: green; font-weight: bold;">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session()->has('error'))
    <div style="color: red; font-weight: bold;">
        🚨 {{ session('error') }}
    </div>
@endif

<form action="{{ route('dropbox.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit">Unggah ke Dropbox</button>
</form>