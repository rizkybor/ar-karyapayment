@props([
    'transaction_status' => '',
    'payment_status' => '',
    'document_status' => '',
    'isEditable' => false,
    'isShowPage' => false,
    'document' => [],
    'latestApprover' => '',
    'bankAccounts',
])

@php
    $printOptions = [
        [
            'label' => 'Surat Permohonan Pembayaran',
            'route' => route('management-fee.print-surat', $document['id']),
        ],
        [
            'label' => 'Kwitansi',
            'route' => route('management-fee.print-kwitansi', $document['id']),
        ],
        [
            'label' => 'Invoice',
            'route' => route('management-fee.print-invoice', $document['id']),
        ],
    ];
@endphp

@php
    $statusIsSix = (int) $document_status === 6;
    $isPerbendaharaan = auth()->user()->role === 'perbendaharaan';
    $printPrivy = $statusIsSix && $isPerbendaharaan;
@endphp

<div x-data="{ modalOpen: false }">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-5 gap-4">

        <!-- Box Status -->
        <div class="w-full sm:w-1/2 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5">
            <div class="grid grid-cols-2 gap-4">
                {{-- Status Transaksi --}}
                <div>
                    <x-label for="transaction_status" value="{{ __('Status Tagihan') }}"
                        class="text-gray-800 dark:text-gray-100" />
                    <p class="mt-1 font-semibold {{ $transaction_status ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction_status ? 'Invoice Aktif' : 'Invoice Tidak Aktif' }}
                    </p>
                </div>

                {{-- Status Dokumen --}}
                <div>
                    <x-label for="document_status" value="{{ __('Status Dokumen') }}"
                        class="text-gray-800 dark:text-gray-100" />
                    <x-label-status :status="$document_status" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <x-label for="transaction_status" value="{{ __('Status Pembayaran') }}"
                        class="text-gray-800 dark:text-gray-100" />
                    <p class="mt-1 text-gray-800 dark:text-gray-200 font-semibold">
                        {{ $payment_status ? $payment_status : '-' }}
                    </p>
                </div>

                {{-- Bank Account --}}
                @if ($isEditable && auth()->user()->hasRole('maker'))
                    <div>
                        <x-label for="bank_account_id" value="{{ __('Pilih Akun Bank') }}"
                            class="text-gray-800 dark:text-gray-100" />

                        <select name="bank_account_id" id="bank_account_id"
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            onchange="updateBankAccount(this.value)">
                            <option value="">-- Pilih Akun Bank --</option>
                            @foreach ($bankAccounts as $bank)
                                <option value="{{ $bank->id }}"
                                    {{ old('bank_account_id', $selectedBankId ?? ($document->bank_account_id ?? '')) == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->bank_name }} - {{ $bank->account_number }} ({{ $bank->account_name }})
                                </option>
                            @endforeach
                        </select>

                        @error('bank_account_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

            </div>
        </div>

        <!-- Tombol Action -->
        @if ($isEditable)
            <x-button-action color="teal" icon="right-arrow"
                onclick="window.location.href='{{ route('management-fee.show', $document->id) }}'">
                Process Document
            </x-button-action>
        @endif

        <!-- Tombol Action -->
        {{-- @if ($isShowPage && $transaction_status == '1') --}}
        @if ($isShowPage)
            <div class="flex flex-wrap gap-2 sm:flex-nowrap sm:w-auto sm:items-start">
                @if ($document_status >= 0)
                    @if (!$printPrivy)

                        {{-- Tombol Lihat Biasa --}}
                        <div x-data="{ open: false }" class="relative">
                            <x-button-action @click="open = !open" color="blue" icon="'eye'">
                                Lihat Dokumen
                            </x-button-action>

                            <div x-show="open" @click.away="open = false"
                                class="absolute z-10 mt-2 bg-white border rounded-lg shadow-lg w-56">
                                <ul class="py-2 text-gray-700">
                                    @foreach ($printOptions as $option)
                                        <li>
                                            <a href="{{ $option['route'] }}" target="_blank"
                                                class="text-sm block px-4 py-2 hover:bg-blue-500 hover:text-white">
                                                {{ $option['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @else
                        {{-- Tombol Cetak/Lihat Khusus (Validasi ke Privy terlebih dulu) --}}
                        <div x-data="{ openPrivy: false }" class="relative">
                            <x-button-action color="blue" icon="print" @click="openPrivy = !openPrivy">
                                Cetak Dokumen
                            </x-button-action>

                            <div x-show="openPrivy" x-transition @click.away="openPrivy = false" x-cloak
                                class="absolute z-10 mt-2 bg-white border rounded-lg shadow-lg w-56">
                                <ul class="py-2 text-gray-700">
                                    <template x-for="option in ['letter', 'kwitansi', 'invoice']"
                                        :key="option">
                                        <li>
                                            <button type="button"
                                                @click="openPrivy = false; openPrivyDoc('{{ $document['id'] }}', option)"
                                                class="text-sm block w-full text-left px-4 py-2 hover:bg-blue-500 hover:text-white"
                                                x-text="{
                                                    'letter': 'Cetak Surat Permohonan',
                                                    'kwitansi': 'Cetak Kwitansi',
                                                    'invoice': 'Cetak Invoice'
                                                }[option]">
                                            </button>
                                        </li>
                                    </template>

                                    {{-- Export ZIP tanpa validasi Privy --}}
                                    <li>
                                        <a href="{{ route('management-fee.download-zip', $document['id']) }}"
                                            target="_blank"
                                            class="text-sm block w-full text-left px-4 py-2 hover:bg-blue-500 hover:text-white">
                                            Export ZIP
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                @endif

                @if ($document_status == 103)
                    <x-button-action color="red" icon="eye"
                        onclick="openRejectModal('', true, '{{ $document->reason_rejected }}', '{{ $document->path_rejected }}')">
                        Alasan Pembatalan
                    </x-button-action>
                @endif

                @if (auth()->user()->role !== 'maker')
                    @if (auth()->user()->role === 'perbendaharaan' && $document_status == 6)
                        <!-- Upload Faktur Pajak Button -->
                        <x-button-action color="teal" icon="pencil"
                            onclick="window.location.href='{{ route('management-fee.edit', $document->id) }}'">
                            Update Lampiran
                        </x-button-action>

                        <!-- Reject Button -->
                        <x-button-action color="red" icon="reject"
                            onclick="openRejectModal('{{ route('management-fee.rejected', $document->id) }}')">
                            Batalkan Dokumen
                        </x-button-action>
                    @endif

                    @if (auth()->user()->role === optional($latestApprover)->approver_role &&
                            !in_array($document_status, [102, 103, 6, 'approved', 'finalized']))
                        <!-- Need Info Button -->
                        <x-button-action color="orange" icon="info"
                            data-action="{{ route('management-fee.processRevision', $document['id']) }}"
                            data-title="Need Info" data-button-text="Send"
                            data-button-color="bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-500 dark:hover:bg-yellow-600 dark:focus:ring-yellow-700"
                            onclick="openModal(this)">
                            Need Info
                        </x-button-action>

                        @if (auth()->user()->role === 'pajak')
                            <!-- Upload Faktur Pajak Button -->
                            <x-button-action color="teal" icon="upload"
                                onclick="window.location.href='{{ route('management-fee.edit', $document->id) }}'">
                                Upload Faktur Pajak
                            </x-button-action>
                        @endif

                        <!-- Approve Button -->
                        <x-button-action color="blue" icon="approve"
                            data-action="{{ route('management-fee.processApproval', $document['id']) }}"
                            data-title="Approve Document" data-button-text="Approve"
                            data-button-color="bg-green-500 hover:bg-green-600 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-700"
                            onclick="openModal(this)">
                            Approve
                        </x-button-action>
                    @endif
                @endif

                @if (auth()->user()->role === 'maker')

                    @if ($document_status == 102)
                        <x-button-action color="orange" icon="reply"
                            data-action="{{ route('management-fee.processApproval', $document['id']) }}"
                            data-title="Reply Info" data-button-text="Reply Info"
                            data-button-color="bg-orange-500 hover:bg-orange-600 dark:bg-orange-500 dark:hover:bg-orange-600 dark:focus:ring-orange-700'"
                            onclick="openModal(this)">
                            Reply Info
                        </x-button-action>
                    @endif

                    @if (in_array($document_status, [0, 102]))
                        <x-button-action color="teal" icon="pencil"
                            onclick="window.location.href='{{ route('management-fee.edit', $document->id) }}'">
                            Edit Invoice
                        </x-button-action>
                    @endif

                    @if ($document_status == 0)
                        <x-button-action color="green" icon="send"
                            data-action="{{ route('management-fee.processApproval', $document['id']) }}"
                            data-title="Process Document" data-button-text="Process"
                            data-button-color="bg-green-500 hover:bg-green-600 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-700"
                            onclick="openModal(this)">
                            Process
                        </x-button-action>
                    @endif
                @endif
            </div>
        @endif
    </div>

    <!-- Panggil Komponen Modal dengan Route -->
    <x-modal.global.modal-proccess-global :document="$document" />

    <x-modal.global.modal-reject-global :document-id="$document->id" />
</div>

<!-- JavaScript untuk Update Form Action, Title, Button Submit, dan Warna -->
<script>
    const isMaker = @json(auth()->user()->hasRole('maker'));
</script>

<script>
    function openModal(button) {
        let actionRoute = button.getAttribute('data-action');
        let modalTitle = button.getAttribute('data-title');
        let buttonText = button.getAttribute('data-button-text');
        let buttonColor = button.getAttribute('data-button-color');

        document.querySelector('#modalForm').setAttribute('action', actionRoute);
        document.querySelector('#modalTitle').innerText = modalTitle;
        document.querySelector('#modalSubmitButton').innerText = buttonText;
        document.querySelector('#modalSubmitButton').setAttribute('data-button-color',
            buttonColor);
        document.querySelector('#modalSubmitButton').classList.remove('bg-green-500', 'hover:bg-green-600',
            'bg-orange-500', 'hover:bg-orange-600', 'dark:bg-orange-500', 'dark:hover:bg-orange-600',
            'dark:focus:ring-orange-700', 'dark:bg-green-500', 'dark:hover:bg-green-600',
            'dark:focus:ring-green-700');
        document.querySelector('#modalSubmitButton').classList.add(...buttonColor.split(' '));

        document.querySelector('#modalOverlay').classList.remove('hidden');
    }

    function closeModal() {
        document.querySelector('#modalOverlay').classList.add('hidden');
    }

    function updateBankAccount(bankId) {
        const documentId = "{{ $document->id }}";
        const token = "{{ csrf_token() }}";

        // 🚫 Validasi: role harus maker
        if (!isMaker) {
            showAutoCloseAlert('globalAlertModal', 3000,
                'Anda tidak memiliki izin untuk mengubah akun bank. Hanya pembuat invoice yang dapat melakukannya',
                'error', 'Akses Ditolak!');
            return;
        }

        // 🚫 Validasi: pastikan ada bank yang dipilih
        if (!bankId) {
            showAutoCloseAlert('globalAlertModal', 3000, 'Silakan pilih akun bank terlebih dahulu.', 'warning',
                'Perhatian!');
            return;
        }

        fetch(`/management-fee/${documentId}/update-bank`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    bank_account_id: bankId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // ✅ Show success modal
                    showAutoCloseAlert('globalAlertModal', 3000, 'Akun bank berhasil diperbarui.', 'success',
                        'Berhasil!');
                } else {
                    // ❌ Show failure modal
                    showAutoCloseAlert('globalAlertModal', 3000, 'Gagal memperbarui akun bank.', 'error', 'Gagal!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAutoCloseAlert('globalAlertModal', 3000, 'Terjadi kesalahan saat menyimpan.', 'error',
                    'Kesalahan!');
            });
    }

    function openPrivyDoc(documentId, type) {
        const token = "{{ csrf_token() }}";

        fetch("{{ route('privy.check-doc-status') }}", {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token
                },
                body: JSON.stringify({
                    document_id: documentId,
                    type_document: type
                })
            })
            .then(res => res.json())
            .then(data => {
                const status = data?.data?.status;
                const signedUrl = data?.data?.signed_document;
                console.log(data.data, '<< cek')

                if ((status === 'uploaded' || status === 'completed') && signedUrl) {
                    window.open(signedUrl, '_blank');
                } else {
                    showAutoCloseAlert(
                        'globalAlertModal',
                        3000,
                        `Dokumen belum siap. Status: Privy ${status ?? 'Tidak diketahui'}`,
                        'warning',
                        'Dokumen Sedang Diproses'
                    );
                }
            })
            .catch(error => {
                console.error('Error saat membuka dokumen:', error);
                showAutoCloseAlert('globalAlertModal', 3000, 'Gagal menghubungi Privy', 'error', 'Kesalahan');
            });
    }
</script>
