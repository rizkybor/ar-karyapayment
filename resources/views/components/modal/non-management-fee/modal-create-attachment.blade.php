@props(['nonManfeeDocument'])

<!-- Modal for File Upload -->
<div id="fileUploadModal" class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tambah Lampiran</h3>

        <!-- Form Upload -->
        <form action="{{ route('non-management-fee.attachments.store', ['id' => $nonManfeeDocument->id]) }}"
            method="POST" enctype="multipart/form-data" id="fileUploadForm">

            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="file_name">
                    Nama File
                </label>
                <input type="text" id="file_name" name="file_name"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="file_upload">
                    Upload File (PDF, Max: 100MB)
                </label>
                <input type="file" id="file_upload" name="file"
                    class="mt-1 block w-full text-gray-900 dark:text-gray-300" accept="application/pdf" required>

                <!-- Pesan Error Jika File Melebihi 10MB -->
                <p id="fileErrorMessage" class="text-red-500 text-sm mt-2 hidden">
                    ⚠️ Ukuran file terlalu besar! Maksimal 100MB.
                </p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md"
                    id="closeModalButton">Batal</button>
                <button type="submit" id="submitButton"
                    class="px-4 py-2 rounded-md text-white bg-violet-500 hover:bg-violet-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Button to Open Modal -->
<x-button-action color="violet" class="px-4 py-2 rounded-md" id="openModalButton">
    + Tambah Lampiran
</x-button-action>

<!-- JavaScript untuk Validasi File Size & Menutup Modal -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const fileInput = document.getElementById("file_upload");
        const submitButton = document.getElementById("submitButton");
        const fileErrorMessage = document.getElementById("fileErrorMessage");
        const openModalButton = document.getElementById("openModalButton");
        const closeModalButton = document.getElementById("closeModalButton");
        const modal = document.getElementById("fileUploadModal");

        // ✅ Buka Modal
        openModalButton.addEventListener("click", function() {
            modal.classList.remove("hidden");
        });

        // ✅ Tutup Modal dan Reset Form
        closeModalButton.addEventListener("click", function() {
            document.getElementById("fileUploadForm").reset();
            fileErrorMessage.classList.add("hidden");
            submitButton.disabled = false;
            submitButton.classList.remove("bg-gray-300", "cursor-not-allowed");
            submitButton.classList.add("bg-violet-500", "hover:bg-violet-600");
            modal.classList.add("hidden");
        });

        // ✅ Validasi File Size
        fileInput.addEventListener("change", function() {
            const file = fileInput.files[0];
            const maxSize = 100 * 1024 * 1024;

            if (file) {
                if (file.size > maxSize) {
                    fileErrorMessage.classList.remove("hidden");
                    fileInput.value = "";
                    submitButton.disabled = true;
                    submitButton.classList.add("bg-gray-300", "cursor-not-allowed");
                    submitButton.classList.remove("bg-violet-500", "hover:bg-violet-600");
                } else {
                    fileErrorMessage.classList.add("hidden");
                    submitButton.disabled = false;
                    submitButton.classList.remove("bg-gray-300", "cursor-not-allowed");
                    submitButton.classList.add("bg-violet-500", "hover:bg-violet-600");
                }
            }
        });
    });
</script>
