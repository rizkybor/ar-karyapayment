@props(['manfeeDoc', 'detailPaymentId', 'jenis_biaya', 'account_dummy'])

<!-- Modal for Editing Cost Details -->
<div x-data="{ modalOpen: false }">
    <x-button-action class="px-4 py-2 bg-yellow-500 text-white rounded-md"
        @click="modalOpen = true">Edit</x-button-action>

    <div class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-30 z-50 flex justify-center items-start pt-20"
        x-show="modalOpen" x-cloak>
        <div class="absolute bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full"
            @click.outside="modalOpen = false">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Edit Detail Biaya</h3>

            <form class="text-left"
                action="{{ route('management-fee.detail_payments.update', ['id' => $manfeeDoc->id, 'detail_payment_id' => $detailPaymentId->id]) }}"
                method="POST">
                @csrf
                @method('PUT')

                <!-- Jenis Biaya -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="expense_type">Jenis
                        Biaya</label>
                    <select id="expense_type" name="expense_type"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                        <option value="" disabled>Pilih Jenis Biaya</option>
                        @foreach ($jenis_biaya as $jenis)
                            <option value="{{ str_replace(' ', '_', strtolower($jenis)) }}"
                                {{ $detailPaymentId->expense_type == str_replace(' ', '_', strtolower($jenis)) ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Account -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        for="account">Account</label>
                    <select id="account" name="account"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                        <option value="">Pilih Akun</option>
                        @foreach ($account_dummy as $account)
                            <option value="{{ $account }}"
                                {{ $detailPaymentId->account == $account ? 'selected' : '' }}>
                                {{ $account }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Uraian -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        for="uraian">Uraian</label>
                    <input type="text" id="uraian" name="uraian" value="{{ $detailPaymentId->uraian }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                </div>

                <!-- Nilai Biaya -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="nilai_biaya">Nilai
                        Biaya</label>
                    <input type="text" id="nilai_biaya" name="nilai_biaya"
                        value="{{ $detailPaymentId->nilai_biaya }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md"
                        @click="modalOpen = false">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-violet-500 text-white rounded-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
