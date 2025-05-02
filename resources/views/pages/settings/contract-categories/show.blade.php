<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Detail Kategori Kontrak</h2>

            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Nama Kategori</p>
                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $category->name }}</p>
            </div>

            <div class="flex justify-end mt-6">
                <x-secondary-button onclick="window.location='{{ route('contract-categories.index') }}'">Kembali</x-secondary-button>
            </div>
        </div>
    </div>
</x-app-layout>