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

                @if (auth()->user()->role === 'maker')
                    <x-button-action color="violet" type="button"
                        onclick="window.location='{{ route('non-management-fee.create') }}'">
                        + Data Baru
                    </x-button-action>
                @endif
            </div>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-full xl:col-span-12 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header
                    class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="font-semibold dark:text-gray-100 py-3">Non Management Fee</h2>
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
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">No</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">No Invoice</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">No Kontrak</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Status</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Nama Pemberi Kerja</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Total Nilai Kontrak</div>
                                    </th>
                                    {{-- <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Jangka Waktu</div>
                                    </th> --}}
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Nama Kontrak</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Expired Status</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Expired Date</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Total</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Action</div>
                                    </th>
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

        {{-- <x-modal.global.modal-alert-global id="globalAlertModal" type="success" title="Berhasil!"
            message="Data berhasil disimpan." timeout="3000" /> --}}
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    <script>
        moment.locale('id'); // Setel bahasa ke Bahasa Indonesia
    </script>

    <script>
        $(document).ready(function() {
            let table = $('#nonManfeeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('non-management-fee.datatable') }}",
                pageLength: 10,
                lengthChange: false,
                searching: true,
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
                        orderable: false,
                        searchable: false,
                        className: 'p-2 whitespace-nowrap text-sm',
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number',
                        className: 'p-2 whitespace-nowrap text-left text-sm',
                        render: function(data) {
                            return `<div>${data ?? '-'}</div>`;
                        }
                    },
                    {
                        data: 'contract.contract_number',
                        name: 'contract.contract_number',
                        className: 'p-2 whitespace-nowrap text-left text-sm',
                        render: function(data) {
                            return `<div>${data ?? '-'}</div>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'p-2 whitespace-nowrap text-center text-sm',
                        orderable: false,
                        searchable: true,
                    },
                    {
                        data: 'contract.employee_name',
                        name: 'contract.employee_name',
                        className: 'p-2 whitespace-nowrap text-center text-sm',
                        render: function(data) {
                            return `<div>${data ?? '-'}</div>`;
                        }
                    },
                    {
                        data: 'contract.value',
                        name: 'contract.value',
                        className: 'p-2 whitespace-nowrap text-center text-sm',
                        render: function(data, type, row) {
                            // Jika data NULL atau NaN, tampilkan "Rp 0,00"
                            if (!data || isNaN(parseFloat(data))) {
                                return 'Rp 0,00';
                            }

                            // Format angka dengan 2 desimal sebagai Rupiah
                            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(parseFloat(data));
                        }
                    },
                    // {
                    //     data: 'period',
                    //     name: 'period',
                    //     className: 'p-2 whitespace-nowrap text-center text-sm',
                    //     render: function(data) {
                    //         return `<div>${data ?? '-'} hari</div>`;

                    //     }
                    // },
                    {
                        data: 'contract.title',
                        name: 'contract.title',
                        className: 'p-2 whitespace-nowrap text-left text-sm',
                        render: function(data) {
                            return `<div>${data ?? '-'}</div>`;
                        }
                    },
                    {
                        data: 'expired_at',
                        name: 'expired_at',
                        className: 'p-2 whitespace-nowrap text-center text-sm',
                        render: function(data) {
                            if (!data) return '<div>-</div>';

                            let expiredTime = moment(data);
                            let now = moment();

                            // Cek apakah sudah lewat dari waktu saat ini
                            if (expiredTime.isBefore(now)) {
                                return `<div class="text-red-500">expired ${expiredTime.fromNow(true)} yang lalu</div>`;
                            } else {
                                return `<div class="text-green-500">tersisa ${expiredTime.fromNow(true)} lagi</div>`;
                            }
                        }
                    },
                    {
                        data: 'expired_at',
                        name: 'expired_at',
                        className: 'p-2 whitespace-nowrap text-center text-sm',
                        render: function(data) {
                            if (!data) return '<div>-</div>';
                            return `<div>${moment(data).format('DD-MM-YYYY')}</div>`; // Format menjadi DD-MM-YYYY
                        }
                    },
                    {
                        data: 'total',
                        name: 'total',
                        className: 'p-2 whitespace-nowrap text-center text-sm',
                        defaultContent: '-'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'p-2 whitespace-nowrap text-center',
                        render: function(data, type, row) {
                            let detailUrl =
                                "{{ route('non-management-fee.show', ['id' => ':id']) }}"
                                .replace(':id', row.id);

                            let editUrl = "{{ route('non-management-fee.edit', ['id' => ':id']) }}"
                                .replace(':id', row.id);

                            let deleteUrl = "{{ route('non-management-fee.destroy', ':id') }}"
                                .replace(':id', row.id);

                            let buttons = `
                            <div class="text-center flex items-center justify-center gap-2">
                                <!-- Tombol View -->
                                <div class="relative group">
                                    <button class="bg-violet-500 text-white p-2 rounded-lg hover:bg-violet-600 transition-all duration-200"
                                            onclick="window.location.href='${detailUrl}'">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 12s-3-4-7-4-7 4-7 4 3 4 7 4 7-4 7-4z"/>
                                        <circle cx="12" cy="12" r="2"/>
                                        </svg>
                                    </button>

                                    <!-- Tooltip -->
                                    <span class="absolute w-auto px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded-md shadow-md 
                                                opacity-0 group-hover:opacity-100 transition-opacity duration-200 -top-8 left-1/2 transform -translate-x-1/2">
                                        Lihat Detail
                                    </span>
                                </div>`;

                            // ✅ Ambil teks status tanpa elemen HTML
                            let statusText = $("<div>").html(row.status).text().trim();
                            let userRole = "{{ auth()->user()->role }}";

                            // ✅ Jika status adalah "Draft" & "Checked by Pajak", tampilkan tombol Edit untuk role "pajak"
                            if (statusText === "Checked by Pajak" && userRole === "pajak") {
                                buttons += `
                                <div class="relative group">
                                    <button class="bg-yellow-500 text-white p-2 rounded-lg hover:bg-yellow-600 transition-all duration-200"
                                            onclick="window.location.href='${editUrl}'">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M11 17h2M15.354 5.354l3.292 3.292a1 1 0 010 1.414L7.414 21H4v-3.414l11.646-11.646a1 1 0 011.414 0z"/>
                                        </svg>
                                    </button>

                                    <!-- Tooltip -->
                                    <span class="absolute w-auto px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded-md shadow-md 
                                                opacity-0 group-hover:opacity-100 transition-opacity duration-200 -top-8 left-1/2 transform -translate-x-1/2">
                                        Edit Data
                                    </span>
                                </div>`;
                            }

                            // ✅ Jika status adalah "Draft" atau "Revised" & BUKAN "Checked by Pajak", tampilkan tombol Edit untuk role "maker"
                            if (statusText === "Draft" || statusText === "Revised" && userRole ===
                                "maker") {
                                buttons += `
                                <div class="relative group">
                                    <button class="bg-yellow-500 text-white p-2 rounded-lg hover:bg-yellow-600 transition-all duration-200"
                                            onclick="window.location.href='${editUrl}'">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M11 17h2M15.354 5.354l3.292 3.292a1 1 0 010 1.414L7.414 21H4v-3.414l11.646-11.646a1 1 0 011.414 0z"/>
                                        </svg>
                                    </button>

                                    <!-- Tooltip -->
                                    <span class="absolute w-auto px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded-md shadow-md 
                                                opacity-0 group-hover:opacity-100 transition-opacity duration-200 -top-8 left-1/2 transform -translate-x-1/2">
                                        Edit Data
                                    </span>
                                </div>`;
                            }


                            // ✅ Jika status adalah "Draft", tampilkan tombol Delete
                            if (statusText === "Draft") {
                                buttons += `
                                    <!-- Tombol Delete -->
                                    <div class="relative group">
                                        <form action="${deleteUrl}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?');">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-700 transition-all duration-200"
                                                    type="submit">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </form>

                                        <!-- Tooltip -->
                                        <span class="absolute w-auto px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded-md shadow-md 
                                                    opacity-0 group-hover:opacity-100 transition-opacity duration-200 -top-8 left-1/2 transform -translate-x-1/2">
                                            Hapus Data
                                        </span>
                                    </div>
                                `;
                            }

                            buttons += `</div>`;
                            return buttons;
                        }
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
                }
            });

            // ✅ Event Listener untuk Export Selected
            $('#exportSelected').on('click', function() {
                let selected = [];
                $('.rowCheckbox:checked').each(function() {
                    selected.push($(this).val());
                });

                if (selected.length === 0) {
                    // alert("Pilih minimal satu data untuk diexport!");
                    showAutoCloseAlert('globalAlertModal', 3000, 'Pilih minimal satu data untuk diexport!',
                        'warning', 'Ops!');
                    return;
                }

                let url = "{{ route('non-management-fee.export') }}?ids=" + encodeURIComponent(selected
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
