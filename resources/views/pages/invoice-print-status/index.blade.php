<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                Checklist Print Invoice
            </h1>


            <!-- Right: Buttons -->
            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button-action color="green" id="exportSelected">
                    Beri Tanda Cetak
                </x-button-action>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                <h2 class="font-semibold dark:text-gray-100 py-3">Fitur ini digunakan sebagai flagging Invoice
                    (Management Fee & Non Management Fee) yang sudah pernah di cetak.</h2>
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

                    <!-- Show Entries Dropdown (Hidden on Mobile) -->
                    <div class="hidden sm:flex items-center gap-2">
                        <label for="perPage" class="text-sm font-medium text-gray-700 dark:text-gray-300">Show:</label>
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

                <div class="overflow-x-auto">
                    <table id="invoicePrintStatusTable" class="table-auto w-full">
                        <thead
                            class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                            <tr>
                                <th class="p-2 whitespace-nowrap"><input type="checkbox" id="selectAll"
                                        class="form-checkbox h-5 w-5 text-blue-600"></th>
                                <th class="p-2 whitespace-nowrap text-center">No</th>
                                <th class="p-2 whitespace-nowrap text-left">No Invoice</th>
                                <th class="p-2 whitespace-nowrap text-center">Status Print</th>
                                <th class="p-2 whitespace-nowrap text-center">Tipe</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Show Entries Dropdown (Visible only on Mobile) -->
                <div class="flex items-center gap-2 sm:hidden mt-5">
                    <label for="perPage" class="text-sm font-medium text-gray-700 dark:text-gray-300">Show:</label>
                    <select id="perPage"
                        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                            text-sm text-gray-700 dark:text-gray-200 font-medium px-3 pr-8 py-2 h-9 rounded-lg shadow-sm 
                            focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 transition-all ease-in-out duration-200">
                        <option value="10">10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Pagination di bawah table -->
                <div class="mt-1 flex flex-col sm:flex-row sm:items-center justify-between">
                    <div id="tableInfo" class="text-sm text-gray-500 dark:text-gray-400"></div>
                    <div id="tablePagination"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    <script>
        $(function() {
            // Inisialisasi DataTable
            const table = $('#invoicePrintStatusTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('invoice.print.status.data') }}',
                    error: function(xhr, error, thrown) {
                        console.error('AJAX Error:', error, thrown);
                        showAutoCloseAlert(
                            'globalAlertModal',
                            3000,
                            'Gagal memuat data. Silakan coba lagi.',
                            'error',
                            'Kesalahan Server'
                        );
                    }
                },
                pageLength: 10,
                dom: 'rtip',
                pagingType: "simple",
                responsive: true,
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'p-2 whitespace-nowrap',
                        render: function(data) {
                            return `<input type="checkbox" class="rowCheckbox form-checkbox h-5 w-5 text-blue-600" value="${data}">`;
                        }
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number',
                        className: 'text-left p-2 whitespace-nowrap text-sm'
                    },
                    {
                        data: 'status_print',
                        name: 'status_print',
                        className: 'text-center p-2 whitespace-nowrap text-sm',
                    },
                    {
                        data: 'type',
                        name: 'type',
                        className: 'text-center p-2 whitespace-nowrap text-sm'
                    }
                ],
                drawCallback: function(settings) {
                    // Handle pagination dan checkbox
                    const api = this.api();
                    const pageInfo = api.page.info();

                    // Update select all checkbox
                    $('input.rowCheckbox').off('change').on('change', function() {
                        const total = $('input.rowCheckbox').length;
                        const checked = $('input.rowCheckbox:checked').length;
                        $('#selectAll').prop('checked', total === checked);
                    });
                }
            });

            // Handle search
            $('#searchTable').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Handle page length
            $('#perPage').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Handle select all
            $('#selectAll').on('click', function() {
                const isChecked = this.checked;
                $('input.rowCheckbox').prop('checked', isChecked).trigger('change');
            });

            // Handle update status
            $('#exportSelected').on('click', function() {
                let selected = [];
                $('.rowCheckbox:checked').each(function() {
                    selected.push($(this).val());
                });

                if (selected.length === 0) {
                    showAutoCloseAlert(
                        'globalAlertModal',
                        3000,
                        'Pilih minimal satu invoice!',
                        'warning',
                        'Tidak Ada Pilihan'
                    );
                    return;
                }


                console.log('Data yang akan dikirim:', selected); // Debug

                $.ajax({
                    url: "{{ route('invoice.print.status.update') }}",
                    type: 'POST',
                    data: {
                        ids: selected,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log('Response:', response); // Debug
                        if (response.success) {
                            showAutoCloseAlert(
                                'globalAlertModal',
                                3000,
                                response.message,
                                'success',
                                'Berhasil!'
                            );
                            $('#invoicePrintStatusTable').DataTable().ajax.reload(null, false);
                        } else {
                            showAutoCloseAlert(
                                'globalAlertModal',
                                3000,
                                'Error: ' + response.message,
                                'error',
                                'Gagal!'
                            );
                        }

                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr.responseJSON); // Debug
                        let errorMsg = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            'Terjadi kesalahan pada server';

                        showAutoCloseAlert(
                            'globalAlertModal',
                            3000,
                            'Error: ' + errorMsg,
                            'error',
                            'Gagal!'
                        );
                    }
                });
            });

            // Debugging: Log events
            table.on('xhr.dt', function(e, settings, json) {
                console.log('Data received:', json);
            });
        });
    </script>
</x-app-layout>
