<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Invoice (Billing)</h1>

            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button-action color="green" id="exportSelected">Export Selected</x-button-action>
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
                    <h2 class="font-semibold dark:text-gray-100 py-3">Management Non Fee</h2>
                </header>
                <div class="p-3">
                    <!-- Table Controls -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                       

                        <div class="relative">
                            <input type="search" id="searchTable"
                                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                                text-sm text-gray-700 dark:text-gray-200 font-medium px-3 pr-10 py-2 h-9 rounded-lg shadow-sm 
                                focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all ease-in-out duration-200"
                                placeholder="Search...">
                        </div>

                        <div class="flex items-center gap-2">
                            <label for="perPage"
                                class="text-sm font-medium text-gray-700 dark:text-gray-300">Show:</label>
                            <select id="perPage"
                                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                                    text-sm text-gray-700 dark:text-gray-200 font-medium px-3 pr-8 py-2 h-9 rounded-lg shadow-sm 
                                    focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all ease-in-out duration-200">
                                <option value="10">10</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table id="nonManfeeTable" class="table-auto w-full">
                            <thead
                                class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                                <tr>
                                    <th class="p-2 whitespace-nowrap"><input type="checkbox" id="selectAll"
                                            class="form-checkbox h-5 w-5 text-blue-600"></th>
                                    <th class="p-2 whitespace-nowrap">No</th>
                                    <th class="p-2 whitespace-nowrap">No Kontrak</th>
                                    <th class="p-2 whitespace-nowrap">Nama Pemberi Kerja</th>
                                    <th class="p-2 whitespace-nowrap">Total Nilai Kontrak</th>
                                    <th class="p-2 whitespace-nowrap">Jangka Waktu</th>
                                    <th class="p-2 whitespace-nowrap">Termin Invoice</th>
                                    <th class="p-2 whitespace-nowrap">Total</th>
                                    <th class="p-2 whitespace-nowrap">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{-- <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400" id="tableInfo"></p>
                        <div id="tablePagination" class="flex gap-2"></div>
                    </div> --}}

                    <!-- Pagination di bawah table -->
                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                         <!-- Menampilkan informasi jumlah data -->
                         <p class="text-sm text-gray-500 dark:text-gray-400">
                            Showing <span class="font-medium">1</span> to
                            <span class="font-medium">
                               1
                            </span> of
                            <span class="font-medium">1</span> documents
                        </p>
                        <!-- Menggunakan Komponen Pagination -->
                        <x-pagination-numeric  />
                    </div>
                </div>

            </div>
        </div>
    </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            let table = $('#nonManfeeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('management-non-fee.datatable') }}",
                pageLength: 10,
                lengthChange: false,
                searching: false,
                dom: 'rtip',
                responsive: true,
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<input type="checkbox" class="rowCheckbox form-checkbox h-5 w-5 text-blue-600" value="${data}">`;
                        }
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'contract.contract_number',
                        name: 'contract.contract_number',
                        className: 'text-left'
                    },
                    {
                        data: 'contract.employee_name',
                        name: 'contract.employee_name',
                        className: 'text-center'
                    },
                    {
                        data: 'contract.value',
                        name: 'contract.value',
                        className: 'text-center',
                        render: function(data) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                        }
                    },
                    {
                        data: 'period',
                        name: 'period',
                        className: 'text-center'
                    },
                    {
                        data: 'termin_invoice',
                        name: 'termin_invoice',
                        className: 'text-center',
                        render: function(data, type, row) {
                            let detailUrl =
                                "{{ route('management-non-fee.show', ['document_id' => ':id']) }}"
                                .replace(':id', row.id);
                            return `<button class="bg-violet-500 text-white px-4 py-2 rounded-lg hover:bg-violet-700" 
                        onclick="window.location.href='${detailUrl}'">
                        Detail Termin</button>`;
                        }
                    },
                    {
                        data: 'total',
                        name: 'total',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            let editUrl =
                                "{{ route('management-non-fee.edit.index', ['document_id' => ':id']) }}"
                                .replace(':id', row.id);
                            return `<button class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-700"
                        onclick="window.location.href='${editUrl}'">
                        Edit</button>`;
                        }
                    }
                ],
                infoCallback: function(settings, start, end, max, total, pre) {
                    // return `Showing ${start} to ${end} of ${total} documents`;
                },
                drawCallback: function(settings) {
                    $('#tablePagination').html($('.dataTables_paginate'));
                }
            });

            // ✅ Event Listener untuk Export Selected
            $('#exportSelected').on('click', function() {
                let selected = [];
                $('.rowCheckbox:checked').each(function() {
                    selected.push($(this).val());
                });

                if (selected.length === 0) {
                    alert("Pilih minimal satu data untuk diexport!");
                    return;
                }

                let url = "{{ route('management-non-fee.export') }}?ids=" + encodeURIComponent(selected
                    .join(","));
                window.open(url, '_blank');
            });

            // ✅ Custom Search Bar
            $('#searchTable').on('keyup', function() {
                table.search(this.value).draw();
            });

            // ✅ Custom Dropdown Entries
            $('#perPage').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // ✅ Checkbox Select All
            $('#selectAll').on('click', function() {
                let rows = table.rows({
                    search: 'applied'
                }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });
        });
    </script>
</x-app-layout>
