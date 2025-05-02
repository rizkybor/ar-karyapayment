<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">
        <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-100">Edit Kategori Kontrak</h2>

        <form action="{{ route('contract-categories.update', $category->id) }}" method="POST" class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Kategori</label>
                <input type="text" name="name" id="name"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                       value="{{ old('name', $category->name) }}" required>
                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end mt-6">
                <x-secondary-button onclick="window.location='{{ route('contract-categories.index') }}'">Batal</x-secondary-button>
                <x-button type="submit" class="ml-2">Update</x-button>
            </div>
        </form>
    </div>
</x-app-layout>