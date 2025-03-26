<div id="rejectDocumentModal" class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Batalkan Dokumen</h3>

        <form id="rejectDocumentForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama File</label>
                <input type="text" name="file_name" required class="form-input w-full rounded-md">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload File (PDF)</label>
                <input type="file" name="file" accept="application/pdf" required class="form-input w-full">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-500 text-white rounded-md">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Batalkan</button>
            </div>
        </form>
    </div>
</div>


<script>
    function openRejectModal(actionUrl) {
        const form = document.getElementById('rejectDocumentForm');
        form.setAttribute('action', actionUrl);
        document.getElementById('rejectDocumentModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectDocumentModal').classList.add('hidden');
        document.getElementById('rejectDocumentForm').reset();
    }
</script>