<form action="{{ route('dropbox.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit">Unggah ke Dropbox</button>
</form>