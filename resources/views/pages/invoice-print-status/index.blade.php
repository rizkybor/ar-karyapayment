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
                <h2 class="font-semibold dark:text-gray-100 py-3">Fitur ini digunakan sebagai flagging Invoice (Management Fee & Non Management Fee) yang sudah pernah di cetak.</h2>
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
            $('#invoicePrintStatusTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('invoice.print.status.data') }}',
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
                        render: function(data) {
                            return data == 1 ?
                                '<span class="text-green-600 font-semibold">Sudah</span>' :
                                '<span class="text-red-600 font-semibold">Belum</span>';
                        }
                    },
                    {
                        data: 'type',
                        name: 'type',
                        className: 'text-center p-2 whitespace-nowrap text-sm'
                    }
                ],
                infoCallback: function(settings, start, end, max, total, pre) {
                    return `
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Showing <span class="font-medium">${start}</span> to
                            <span class="font-medium">${end}</span> of
                            <span class="font-medium">${total}</span> documents
                        </p>
                    `;
                },
                drawCallback: function(settings) {
                    let api = this.api();
                    let pageInfo = api.page.info();
                    let currentPage = pageInfo.page + 1;
                    let totalPages = pageInfo.pages;

                    let paginationHtml = `
                    <div class="flex justify-center">
                        <nav class="flex" role="navigation" aria-label="Navigation">
                            <div class="mr-2">
                                ${currentPage > 1 ? `
                                                    <button data-page="${currentPage - 2}"
                                                        class="inline-flex items-center justify-center rounded-lg leading-5 px-2.5 py-2 bg-white dark:bg-gray-800 
                                                        border border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 shadow-sm">
                                                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                                                            <path d="M9.4 13.4l1.4-1.4-4-4 4-4-1.4-1.4L4 8z" />
                                                        </svg>
                                                    </button>` : `
                                                    <span class="inline-flex items-center justify-center rounded-lg leading-5 px-2.5 py-2 bg-white dark:bg-gray-800 
                                                        border border-gray-200 dark:border-gray-700/60 text-gray-300 dark:text-gray-600 shadow-sm">
                                                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                                                            <path d="M9.4 13.4l1.4-1.4-4-4 4-4-1.4-1.4L4 8z" />
                                                        </svg>
                                                    </span>`}
                            </div>
                            <ul class="inline-flex text-sm font-medium -space-x-px rounded-lg shadow-sm">`;

                    for (let i = 1; i <= totalPages; i++) {
                        paginationHtml += i === currentPage ?
                            `<li>
                            <span class="inline-flex items-center justify-center rounded-lg leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 
                                border border-gray-200 dark:border-gray-700/60 text-violet-500">
                                ${i}
                            </span>
                        </li>` :
                            `<li>
                            <button data-page="${i - 1}"
                                class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 
                                hover:bg-gray-50 dark:hover:bg-gray-900 border border-gray-200 dark:border-gray-700/60 
                                text-gray-600 dark:text-gray-300">
                                ${i}
                            </button>
                        </li>`;
                    }

                    paginationHtml += `
                            </ul>
                            <div class="ml-2">
                                ${currentPage < totalPages ? `
                                                    <button data-page="${currentPage}"
                                                        class="inline-flex items-center justify-center rounded-lg leading-5 px-2.5 py-2 bg-white dark:bg-gray-800 
                                                        border border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 shadow-sm">
                                                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                                                            <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" />
                                                        </svg>
                                                    </button>` : `
                                                    <span class="inline-flex items-center justify-center rounded-lg leading-5 px-2.5 py-2 bg-white dark:bg-gray-800 
                                                        border border-gray-200 dark:border-gray-700/60 text-gray-300 dark:text-gray-600 shadow-sm">
                                                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                                                            <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" />
                                                        </svg>
                                                    </span>`}
                            </div>
                        </nav>
                    </div>`;

                    // Set pagination HTML
                    $('#tablePagination').html(paginationHtml);

                    // Delegate pagination click
                    $('#tablePagination').off('click', 'button[data-page]').on('click',
                        'button[data-page]',
                        function() {
                            let page = $(this).data('page');
                            api.page(page).draw('page');
                        });

                    // Re-bind per-row checkbox logic to update #selectAll
                    $('input.rowCheckbox').off('change').on('change', function () {
                        const totalCheckbox = $('input.rowCheckbox').length;
                        const checkedCheckbox = $('input.rowCheckbox:checked').length;

                        // Update #selectAll based on checked status
                        $('#selectAll').prop('checked', totalCheckbox === checkedCheckbox);
                    });
                }
            });

            // ✅ Event Listener untuk Export Selected
            $('#exportSelected').on('click', function() {
                let selected = [];
                $('.rowCheckbox:checked').each(function() {
                    selected.push($(this).val());
                });

                if (selected.length === 0) {
                    showAutoCloseAlert('globalAlertModal', 3000,
                        'Pilih minimal satu data untuk diperbarui!', 'warning', 'Ops!');
                    return;
                }

                $.ajax({
                    url: "{{ route('invoice.print.status.update') }}",
                    type: 'POST',
                    data: {
                        ids: selected,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log(response,'<< cek')
                        showAutoCloseAlert('globalAlertModal', 3000,
                            'Status berhasil diperbarui!', 'success', 'Berhasil!');
                        $('#invoicePrintStatusTable').DataTable().ajax.reload();
                    },
                    error: function() {
                        showAutoCloseAlert('globalAlertModal', 3000,
                            'Terjadi kesalahan saat memperbarui data.', 'danger', 'Gagal!');
                    }
                });
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
            $('#selectAll').on('click', function () {
                const isChecked = this.checked;
                const rows = $('#invoicePrintStatusTable').DataTable().rows({ search: 'applied' }).nodes();
                $('input.rowCheckbox', rows).prop('checked', isChecked).trigger('change');
            });
        });
    </script>
</x-app-layout>
