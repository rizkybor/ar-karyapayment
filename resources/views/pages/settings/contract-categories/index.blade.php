<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <!-- Left: Title -->
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                Contract Categories
            </h1>

            <!-- Right: Buttons -->
            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button-action color="violet" type="button"
                    onclick="window.location='{{ route('contract-categories.create') }}'">
                    + Data Baru
                </x-button-action>
            </div>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-full xl:col-span-12 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header
                    class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <x-search-form placeholder="Cari kategori…" :value="$search ?? ''" />
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="perPage" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Show:
                        </label>
                        <div class="relative">
                            <select id="perPage"
                                class="appearance-none border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                                text-sm text-gray-700 dark:text-gray-200 font-medium 
                                px-3 pr-8 py-2 h-9 rounded-lg shadow-sm focus:ring focus:ring-blue-300 
                                dark:focus:ring-blue-700 transition-all ease-in-out duration-200"
                                onchange="changePerPage()">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="p-3">
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full">
                            <thead
                                class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                                <tr>
                                    <th class="p-2 text-left">#</th>
                                    <th class="p-2 text-left">Nama Kontrak</th>
                                    <th class="p-2 text-right">Action</th> <!-- ✅ Align header ke kanan -->
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                                @forelse ($categories as $index => $category)
                                    <tr>
                                        <td class="p-2">{{ $categories->firstItem() + $index }}</td>
                                        <td class="p-2">{{ $category->name }}</td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-2"> <!-- ✅ Align tombol ke kanan -->
                                                <x-button-action color="violet" icon="view"
                                                    onclick="window.location.href='{{ route('contract-categories.show', $category->id) }}'">
                                                </x-button-action>
                                                <x-button-action color="yellow" icon="pencil"
                                                    onclick="window.location.href='{{ route('contract-categories.edit', $category->id) }}'">
                                                </x-button-action>
                                                <form action="{{ route('contract-categories.destroy', $category->id) }}" method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-button-action color="red" icon="trash" type="submit">
                                                    </x-button-action>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                            Tidak ada data kategori.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Showing <span class="font-medium">{{ $categories->firstItem() }}</span> to
                            <span class="font-medium">{{ $categories->lastItem() }}</span> of
                            <span class="font-medium">{{ $categories->total() }}</span> categories
                        </p>

                        <x-pagination-numeric :data="$categories" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function changePerPage() {
            let perPage = document.getElementById("perPage").value;
            window.location.href = "{{ route('contract-categories.index') }}?per_page=" + perPage;
        }
    </script>
</x-app-layout>