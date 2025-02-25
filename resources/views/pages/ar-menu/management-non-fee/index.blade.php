<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <!-- Left: Title -->
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                Invoice (Billing)
            </h1>

            <!-- Right: Buttons -->
            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button-action color="green" id="exportSelected">
                    Export Selected
                </x-button-action>

                <x-button-action color="violet" type="button"
                    onclick="window.location='{{ route('management-non-fee.create') }}'">
                    + Data Baru
                </x-button-action>
            </div>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-full xl:col-span-12 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header
                    class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- Left: Title -->
                    <h2 class="font-semibold dark:text-gray-100">Management Non Fee Docs</h2>

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
                                        <input type="checkbox" id="selectAll"
                                            class="form-checkbox h-5 w-5 text-blue-600">
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">No</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">No Kontrak</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Nama Pemberi Kerja</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Total Nilai Kontrak</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Jangka Waktu</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Termin Invoice</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Total</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                                @php $i = 1; @endphp
                                @foreach ($NonManfeeDocs as $NonManfeeDoc)
                                    <tr>
                                        <td class="p-2 whitespace-nowrap">
                                            <input type="checkbox"
                                                class="rowCheckbox form-checkbox h-5 w-5 text-blue-600"
                                                value="{{ $NonManfeeDoc->id }}">
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">{{ $i++ }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-left">{{ $NonManfeeDoc->contract->contract_number }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">{{ $NonManfeeDoc->contract->employee_name }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">Rp
                                                {{ number_format($NonManfeeDoc->contract->value, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">{{ $NonManfeeDoc->period }}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">
                                                <x-button-action color="violet"
                                                onclick="window.location.href='{{ route('management-non-fee.show', ['id' => $NonManfeeDoc->id]) }}'">
                                                Detail Termin
                                            </x-button-action>
                                            </div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center">-</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-center flex items-center justify-center gap-2">
                                                <x-button-action color="yellow" icon="pencil"
                                                onclick="window.location.href='{{ route('management-non-fee.edit', ['id' => $NonManfeeDoc->id]) }}'">
                                            </x-button-action>
                                                <form
                                                    action="{{ route('management-non-fee.destroy', $NonManfeeDoc->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-button-action color="red" icon="trash" type="submit">
                                                    </x-button-action>
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
                            Showing <span class="font-medium">{{ $NonManfeeDocs->firstItem() }}</span> to
                            <span class="font-medium">
                                {{ min($NonManfeeDocs->firstItem() + request('per_page', 10) - 1, request('per_page', 10)) }}
                            </span> of
                            <span class="font-medium">{{ $NonManfeeDocs->total() }}</span> documents
                        </p>

                        <!-- Pagination links -->
                        <div class="py-2">
                            {{ $NonManfeeDocs->appends(['per_page' => request('per_page', 10)])->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <script>
        function changePerPage() {
            let perPage = document.getElementById("perPage").value;
            window.location.href = "{{ route('management-non-fee.index') }}?per_page=" + perPage;
        }

        document.getElementById("selectAll").addEventListener("click", function() {
            let checkboxes = document.querySelectorAll(".rowCheckbox");
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        document.getElementById("exportSelected").addEventListener("click", function() {
            let selected = [];
            document.querySelectorAll(".rowCheckbox:checked").forEach(checkbox => {
                selected.push(checkbox.value);
            });

            if (selected.length === 0) {
                alert("Pilih minimal satu data untuk diexport!");
                return;
            }

            // Menggunakan window.open agar browser mengunduh file
            let url = `{{ route('management-non-fee.export') }}?ids=` + encodeURIComponent(selected.join(","));
            window.open(url, '_blank');
        });
    </script>

</x-app-layout>
