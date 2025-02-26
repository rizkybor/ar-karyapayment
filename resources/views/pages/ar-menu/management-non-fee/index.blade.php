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
                    <div>
                        <!-- Left: Title -->
                        <h2 class="font-semibold dark:text-gray-100 py-3">Management Non Fee</h2>
                    </div>
                </header>
                <div class="p-3">
                    <!-- Table Controls: Search & Show Entries -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <label for="perPage"
                                class="text-sm font-medium text-gray-700 dark:text-gray-300">Show:</label>
                            <div class="relative">
                                <select id="perPage"
                                    class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                                    text-sm text-gray-700 dark:text-gray-200 font-medium px-3 pr-8 py-2 h-9 rounded-lg shadow-sm 
                                    focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all ease-in-out duration-200">
                                    <option value="10">10</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <!-- Icon Chevron -->
                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <input type="search" id="searchTable"
                                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                                text-sm text-gray-700 dark:text-gray-200 font-medium px-3 pr-10 py-2 h-9 rounded-lg shadow-sm 
                                focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all ease-in-out duration-200"
                                placeholder="Search...">
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table id="nonManfeeTable" class="table-auto w-full">
                            <thead
                                class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                                <tr>
                                    <th class="p-2 whitespace-nowrap">
                                        <input type="checkbox" id="selectAll"
                                            class="form-checkbox h-5 w-5 text-blue-600">
                                    </th>
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
                            <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60"></tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400" id="tableInfo"></p>
                        <div id="tablePagination"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    </div>

    <script>
        // function changePerPage() {
        //     let perPage = document.getElementById("perPage").value;
        //     window.location.href = "{{ route('management-non-fee.index') }}?per_page=" + perPage;
        // }

        // document.getElementById("selectAll").addEventListener("click", function() {
        //     let checkboxes = document.querySelectorAll(".rowCheckbox");
        //     checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        // });

        // document.getElementById("exportSelected").addEventListener("click", function() {
        //     let selected = [];
        //     document.querySelectorAll(".rowCheckbox:checked").forEach(checkbox => {
        //         selected.push(checkbox.value);
        //     });

        //     if (selected.length === 0) {
        //         alert("Pilih minimal satu data untuk diexport!");
        //         return;
        //     }

        //     // Menggunakan window.open agar browser mengunduh file
        //     let url = `{{ route('management-non-fee.export') }}?ids=` + encodeURIComponent(selected.join(","));
        //     window.open(url, '_blank');
        // });
    </script>

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
                dom: 'rtip',
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
                            return `<x-button-action color="violet" onclick="window.location.href='{{ route('management-non-fee.show', ['document_id' => '__ID__']) }}'.replace('__ID__', row.id)">
                            Detail Termin
                        </x-button-action>`;
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
                        className: 'text-center'
                    }
                ],
                drawCallback: function(settings) {
                    let pageInfo = table.page.info();
                    $('#tableInfo').text(
                        `Showing ${pageInfo.start + 1} to ${pageInfo.end} of ${pageInfo.recordsTotal} entries`
                        );
                    $('#tablePagination').html($('.dataTables_paginate'));
                }
            });

            // Search Input
            $('#searchTable').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Show Entries Dropdown
            $('#perPage').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Checkbox Select All
            $('#selectAll').on('click', function() {
                let rows = table.rows({
                    search: 'applied'
                }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });
        });
    </script>

</x-app-layout>
