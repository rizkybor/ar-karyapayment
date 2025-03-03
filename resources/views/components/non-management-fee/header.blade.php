@props([
    'transaction_status' => '',
    'document_status' => '',
    'isEditable' => false,
    'isShowPage' => false,
    'document' => [],
    'latestApprover' => '',
])

<div x-data="{ modalOpen: false }">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-5 gap-4">

        <!-- Box Status -->
        <div class="w-full sm:w-1/2 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5">
            <div class="grid grid-cols-2 gap-4">
                {{-- Status Transaksi --}}
                <div>
                    <x-label for="transaction_status" value="{{ __('Status Transaksi') }}"
                        class="text-gray-800 dark:text-gray-100" />
                    <p class="mt-1 text-gray-800 dark:text-gray-200 font-semibold">
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

            <br />

            <div class="grid grid-cols-1 gap-4">
                {{-- Jenis --}}
                <div>
                    <x-label for="transaction_status" value="{{ __('Jenis') }}"
                        class="text-gray-800 dark:text-gray-100" />
                    <p class="mt-1 text-gray-800 dark:text-gray-200 font-semibold">
                        Non Management Fee
                    </p>
                </div>
            </div>
        </div>

        <!-- Tombol Action -->
        @if ($isShowPage)
            <div class="flex flex-wrap gap-2 sm:flex-nowrap sm:w-auto sm:items-start">
                @if (auth()->user()->role !== 'maker')
                    @if ($document_status == 0)
                        <x-button-action color="blue" icon="print">Print</x-button-action>
                        <x-button-action color="teal" icon="paid">Paid</x-button-action>
                    @endif

                    @if (auth()->user()->role === $latestApprover->role)
                        <x-button-action color="orange" icon="info">Need Info</x-button-action>
                        <x-button-action color="red" icon="reject">Reject</x-button-action>


                        <x-button-action color="green" icon="process"
                            data-action="{{ route('non-management-fee.processApproval', $document['id']) }}"
                            data-title="Approve Document" data-button-text="Approve"
                            data-button-color="bg-blue-500 hover:bg-blue-600" onclick="openModal(this)">
                            Approve
                        </x-button-action>
                    @endif
                @endif

                @if (auth()->user()->role === 'maker')

                    @if ($document_status == 0)
                        <x-button-action color="orange" icon="reply"
                            data-action="{{ route('non-management-fee.processRevision', $document['id']) }}"
                            data-title="Reply Info" data-button-text="Reply Info"
                            data-button-color="bg-orange-500 hover:bg-orange-600" onclick="openModal(this)">
                            Reply Info
                        </x-button-action>
                    @endif

                    @if ($document_status == 0)
                        <x-button-action color="green" icon="process"
                            data-action="{{ route('non-management-fee.processApproval', $document['id']) }}"
                            data-title="Process Document" data-button-text="Process"
                            data-button-color="bg-green-500 hover:bg-green-600" onclick="openModal(this)">
                            Process
                        </x-button-action>
                    @endif
                @endif
            </div>
        @endif
    </div>

    <!-- Panggil Komponen Modal dengan Route -->
    <x-modal.global.modal-proccess-global :document="$document" />
</div>

<!-- JavaScript untuk Update Form Action, Title, Button Submit, dan Warna -->
<script>
    function openModal(button) {
        let actionRoute = button.getAttribute('data-action'); // Ambil route dari tombol
        let modalTitle = button.getAttribute('data-title'); // Ambil title dari tombol
        let buttonText = button.getAttribute('data-button-text'); // Ambil teks button submit
        let buttonColor = button.getAttribute('data-button-color'); // Ambil warna tombol submit

        document.querySelector('#modalForm').setAttribute('action', actionRoute); // Set action baru di form
        document.querySelector('#modalTitle').innerText = modalTitle; // Set title baru di modal
        document.querySelector('#modalSubmitButton').innerText = buttonText; // Set teks button submit
        document.querySelector('#modalSubmitButton').setAttribute('data-button-color',
        buttonColor); // Set warna tombol submit
        document.querySelector('#modalSubmitButton').classList.remove('bg-green-500', 'hover:bg-green-600',
            'bg-orange-500', 'hover:bg-orange-600');
        document.querySelector('#modalSubmitButton').classList.add(...buttonColor.split(' ')); // Ganti warna button

        document.querySelector('#modalOverlay').classList.remove('hidden'); // Tampilkan modal
    }

    function closeModal() {
        document.querySelector('#modalOverlay').classList.add('hidden'); // Sembunyikan modal
    }
</script>
