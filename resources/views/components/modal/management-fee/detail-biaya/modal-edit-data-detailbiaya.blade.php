@props(['manfeeDoc', 'detailPaymentId', 'jenis_biaya', 'account_detailbiaya'])

<!-- Modal for Editing Cost Details -->
<div x-data="{
    modalOpen: false,
    expense_type: '{{ old('expense_type', $detailPaymentId->expense_type) }}',
    account: '{{ old('account', $detailPaymentId->account) }}',
    account_name: '{{ old('account_name', $detailPaymentId->account_name) }}',
    accountId: '{{ old('accountId', $detailPaymentId->accountId ?? '') }}',
    nilai_biaya: '{{ old('nilai_biaya', number_format($detailPaymentId->nilai_biaya, 0, ',', '.')) }}',
    init() {
        // Format nilai biaya saat modal dibuka
        this.$watch('nilai_biaya', (value) => {
            this.nilai_biaya = this.formatCurrency(value);
        });
    },
    formatCurrency(value) {
        let num = value.replace(/\D/g, '');
        return num ? new Intl.NumberFormat('id-ID').format(num) : '';
    }
}">
    <x-button-action class="px-4 py-2 text-white rounded-md" @click="modalOpen = true" color="yellow"
        icon="pencil">Edit</x-button-action>

    <div class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center" x-show="modalOpen" x-cloak>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full"
            @click.outside="modalOpen = false">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Edit Detail Biaya</h3>

            <form method="POST"
                action="{{ route('management-fee.detail_payments.update', ['id' => $manfeeDoc->id, 'detail_payment_id' => $detailPaymentId->id]) }}">
                @csrf
                @method('PUT')

                <!-- Jenis Biaya -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">Jenis
                        Biaya</label>

                    <select name="expense_type" x-model="expense_type"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                        <option value="" disabled>Pilih Jenis Biaya</option>
                        @foreach ($jenis_biaya as $jenis)
                            <option value="{{ $jenis }}"
                                {{ $detailPaymentId->expense_type == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Account -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">Account</label>
                    <select name="account" x-model="account"
                        @change="
                            account_name = $event.target.selectedOptions[0].dataset.name
                            accountId = $event.target.selectedOptions[0].dataset.id;
                            "
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                        <option value="">Pilih Akun</option>
                        @foreach ($account_detailbiaya as $akun)
                            <option value="{{ $akun['no'] }}" data-name="{{ $akun['name'] }}" data-id="{{ $akun['id'] }}"
                                {{ old('account', $detailPaymentId->account) == $akun['no'] ? 'selected' : '' }}>
                                ({{ $akun['no'] }})
                                {{ $akun['name'] }}
                            </option>
                        @endforeach
                    </select>

                    <input type="hidden" name="accountId" x-model="accountId">                    
                    <input type="hidden" name="account_name" x-model="account_name">
                </div>

                <!-- Uraian Dinamis -->
                {{-- <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">Uraian</label>
                    <input type="text" name="uraian" value="{{ old('uraian', $detailPaymentId->uraian) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>

                          value="{{ old('account_name', $detailPaymentId->account_name) }}"
                </div> --}}

                <!-- Uraian -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">Uraian</label>
                    <input type="text" x-model="account_name"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-200 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required readonly>
                </div>


                <!-- Nilai Biaya -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left">Nilai
                        Biaya</label>
                    <input type="text" name="nilai_biaya" x-model="nilai_biaya"
                        @input="nilai_biaya = formatCurrency($event.target.value)"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                </div>

                <div class="flex justify-end gap-2">
                    <x-button-action color="red" class="px-4 py-2 bg-gray-500 text-white rounded-md"
                        @click.prevent="modalOpen = false">Batal</x-button>
                        <x-button-action color="violet" type="submit">Simpan</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
