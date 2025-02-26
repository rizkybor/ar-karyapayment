<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <!-- Left: Title -->
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                Contracts
            </h1>

            <!-- Right: Buttons -->
            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button-action color="violet" type="button" onclick="window.location='{{ route('contracts.create') }}'">
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
                        <!-- Left: Title -->
                        {{-- <h2 class="font-semibold dark:text-gray-100 py-3">Contracts</h2> --}}
                        <x-search-form placeholder="Search…" :value="$search ?? ''" />
                    </div>
                    <!-- Middle: Dropdown jumlah per halaman -->
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
                                <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100
                                </option>
                            </select>
                            <!-- Icon Chevron -->
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
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full">
                            <!-- Table header -->
                            <thead
                                class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                                <tr>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">No</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">No Kontrak</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">Nama Pemberi Kerja</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Tanggal Mulai</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Tanggal Selesai</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Tipe Kontrak</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Action</div>
                                    </th>

                                </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                                @php $i = 1; @endphp
                                @foreach ($contracts as $contract)
                                    <tr>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">{{ $i++ }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-left">{{ $contract->contract_number }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-left">{{ $contract->employee_name }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">
                                                {{ \Carbon\Carbon::parse($contract->start_date)->translatedFormat('l, d F Y') }}
                                            </div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">
                                                {{ \Carbon\Carbon::parse($contract->end_date)->translatedFormat('l, d F Y') }}
                                            </div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">
                                                {{ ucwords(str_replace('_', ' ', $contract->type)) }}</div>

                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center flex justify-center gap-x-2">
                                                <a href="{{ route('contracts.show', $contract->id) }}">
                                                    <x-secondary-button>Details</x-secondary-button>
                                                </a>
                                                <a href="{{ route('contracts.edit', $contract->id) }}">
                                                    <x-edit-button>Edit</x-edit-button>
                                                </a>


                                                <form
                                                    action="{{ route('contracts.destroy', ['contract' => $contract->id]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-delete-button type="submit">Delete</x-delete-button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    <!-- Pagination di bawah table -->
                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <!-- Menampilkan informasi jumlah data -->
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Showing <span class="font-medium">{{ $contracts->firstItem() }}</span> to
                            <span class="font-medium">
                                {{ min($contracts->firstItem() + request('per_page', 10) - 1, request('per_page', 10)) }}
                            </span> of
                            <span class="font-medium">{{ $contracts->total() }}</span> documents
                        </p>

                        <!-- Menggunakan Komponen Pagination -->
                        <x-pagination-numeric :data="$contracts" />
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
        function changePerPage() {
            let perPage = document.getElementById("perPage").value;
            window.location.href = "{{ route('contracts.index') }}?per_page=" + perPage;
        }
    </script>
</x-app-layout>
