@props(['manfeeDoc'])

<!-- Modal for File Upload -->
<div x-data="{ modalOpen: false }">
    <x-button-action class="px-4 py-2 text-white rounded-md" color="violet" @click="modalOpen = true">Tambah
        Deskripsi</x-button-action>
    <div class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center" x-show="modalOpen" x-cloak>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full"
            @click.outside="modalOpen = false">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tambah Lampiran</h3>
            <form action="{{ route('management-fee.descriptions.store', ['id' => $manfeeDoc->id]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="file_name">Masukkan
                        Deskripsi</label>
                    <textarea id="description" name="description"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        rows="4" required></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <x-button-action color="red" class="px-4 py-2 bg-gray-500 text-white rounded-md"
                        @click="modalOpen = false">Batal</x-button>
                        <x-button-action color="violet" type="submit">Simpan</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
