@props(['manfeeDoc'])

<!-- Modal for File Upload -->
<div id="taxUploadModal" class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tambah Faktur Pajak</h3>

        <!-- Form Upload -->
        <form action="{{ route('management-fee.taxs.store', ['id' => $manfeeDoc->id]) }}" method="POST"
            enctype="multipart/form-data" id="taxUploadForm">

            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="file_name">
                    Nama File
                </label>
                <input type="text" id="tax_file_name" name="file_name"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="tax_file_upload">
                    Upload File (PDF, Max: 10MB)
                </label>
                <input type="file" id="tax_file_upload" name="file"
                    class="mt-1 block w-full text-gray-900 dark:text-gray-300" accept="application/pdf" required>

                <!-- Pesan Error Jika File Melebihi 10MB -->
                <p id="taxFileErrorMessage" class="text-red-500 text-sm mt-2 hidden">
                    ⚠️ Ukuran file terlalu besar! Maksimal 10MB.
                </p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md"
                    id="closeTaxModalButton">Batal</button>
                <button type="submit" id="taxSubmitButton"
                    class="px-4 py-2 rounded-md text-white bg-violet-500 hover:bg-violet-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Button to Open Modal -->
<x-button-action color="violet" class="px-4 py-2 rounded-md" id="openTaxModalButton">
    + Tambah Faktur Pajak
</x-button-action>

<!-- JavaScript untuk Validasi File Size & Menutup Modal -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const taxFileInput = document.getElementById("tax_file_upload");
        const taxSubmitButton = document.getElementById("taxSubmitButton");
        const taxFileErrorMessage = document.getElementById("taxFileErrorMessage");
        const openTaxModalButton = document.getElementById("openTaxModalButton");
        const closeTaxModalButton = document.getElementById("closeTaxModalButton");
        const taxModal = document.getElementById("taxUploadModal");

        // ✅ Buka Modal
        openTaxModalButton.addEventListener("click", function() {
            taxModal.classList.remove("hidden");
        });

        // ✅ Tutup Modal dan Reset Form
        closeTaxModalButton.addEventListener("click", function() {
            document.getElementById("taxUploadForm").reset();
            taxFileErrorMessage.classList.add("hidden");
            taxSubmitButton.disabled = false;
            taxSubmitButton.classList.remove("bg-gray-300", "cursor-not-allowed");
            taxSubmitButton.classList.add("bg-violet-500", "hover:bg-violet-600");
            taxModal.classList.add("hidden");
        });

        // ✅ Validasi File Size
        taxFileInput.addEventListener("change", function() {
            const file = taxFileInput.files[0];
            const maxSize = 10 * 1024 * 1024;

            if (file) {
                if (file.size > maxSize) {
                    taxFileErrorMessage.classList.remove("hidden");
                    taxFileInput.value = "";
                    taxSubmitButton.disabled = true;
                    taxSubmitButton.classList.add("bg-gray-300", "cursor-not-allowed");
                    taxSubmitButton.classList.remove("bg-violet-500", "hover:bg-violet-600");
                } else {
                    taxFileErrorMessage.classList.add("hidden");
                    taxSubmitButton.disabled = false;
                    taxSubmitButton.classList.remove("bg-gray-300", "cursor-not-allowed");
                    taxSubmitButton.classList.add("bg-violet-500", "hover:bg-violet-600");
                }
            }
        });
    });
</script>
