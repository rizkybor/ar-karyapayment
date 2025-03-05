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

        @if ($isEditable)
            <x-button-action color="blue" icon="eye"
                onclick="window.location.href='{{ route('non-management-fee.show', $document->id) }}'">
                Process Document
            </x-button-action>
        @endif

        <!-- Tombol Action -->
        @if ($isShowPage)
            <div class="flex flex-wrap gap-2 sm:flex-nowrap sm:w-auto sm:items-start">
                @if (auth()->user()->role !== 'maker')
                    @if ($document_status == 6)
                        <x-button-action color="blue" icon="print">Print</x-button-action>
                        <x-button-action color="teal" icon="paid">Closed Document</x-button-action>
                        <x-button-action color="red" icon="reject">Batalkan Dokumen</x-button-action>
                    @endif

                    @if (auth()->user()->role === optional($latestApprover)->approver_role &&
                            !in_array($document_status, [102, 6, 'approved', 'finalized']))
                        <!-- Need Info Button -->
                        <x-button-action color="orange" icon="info"
                            data-action="{{ route('non-management-fee.processRevision', $document['id']) }}"
                            data-title="Need Info" data-button-text="Send"
                            data-button-color="bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-500 dark:hover:bg-yellow-600 dark:focus:ring-yellow-700"
                            onclick="openModal(this)">
                            Need Info
                        </x-button-action>

                        <!-- Reject Button -->
                        {{-- <x-button-action color="red" icon="reject"
                            data-action="{{ route('non-management-fee.reject', $document['id']) }}"
                            data-title="Reject Document" data-button-text="Reject"
                            data-button-color="bg-red-500 hover:bg-red-600 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-700"
                            onclick="openModal(this)">
                            Reject
                        </x-button-action> --}}

                        <!-- Approve Button -->
                        <x-button-action color="blue" icon="approve"
                            data-action="{{ route('non-management-fee.processApproval', $document['id']) }}"
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
                            data-action="{{ route('non-management-fee.processApproval', $document['id']) }}"
                            data-title="Reply Info" data-button-text="Reply Info"
                            data-button-color="bg-orange-500 hover:bg-orange-600 dark:bg-orange-500 dark:hover:bg-orange-600 dark:focus:ring-orange-700'"
                            onclick="openModal(this)">
                            Reply Info
                        </x-button-action>
                    @endif

                    @if ($document_status == 0)
                        <x-button-action color="green" icon="process"
                            data-action="{{ route('non-management-fee.processApproval', $document['id']) }}"
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
</div>

<!-- JavaScript untuk Update Form Action, Title, Button Submit, dan Warna -->
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
</script>
