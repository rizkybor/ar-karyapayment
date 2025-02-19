<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            {{-- Judul --}}
            <div class="md:col-span-1 flex justify-between">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tambah Kontrak</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Tambahkan informasi kontrak baru, termasuk detail pihak terkait, durasi, dan persyaratan khusus.
                    </p>
                </div>
            </div>
            {{-- Judul End --}}

            {{-- Tambah Data Baru --}}
            <div class="mt-5 md:mt-0 md:col-span-2">
                @csrf
                <form action="" class="max-w-3xl mx-auto w-full">
                    <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="grid grid-cols-1 grid-rows-11 gap-4">
                            <div>
                                <x-label for="nomer_kontrak" value="{{ __('Nomer Kontrak') }}" />
                                <x-input id="nomer_kontrak" type="text" class="mt-1 block w-3/4 min-h-[40px]"
                                    wire:model.live="state.nomer_kontrak" required autocomplete="nomer_kontrak" />
                                <x-input-error for="nomer_kontrak" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="nama_perusahaan" value="{{ __('Nama Perusahaan') }}" />
                                <x-input id="nama_perusahaan" type="text" class="mt-1 block w-full min-h-[40px]"
                                    wire:model.live="state.nama_perusahaan" required autocomplete="nama_perusahaan" />
                                <x-input-error for="nama_perusahaan" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="value" value="{{ __('Value') }}" />
                                <x-input id="value" type="text" class="mt-1 block w-full min-h-[40px]"
                                    wire:model.live="state.value" required autocomplete="value" />
                                <x-input-error for="value" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="start_date" value="{{ __('Start Date') }}" />
                                <x-input id="start_date" type="text" class="mt-1 block w-full min-h-[40px]"
                                    wire:model.live="state.start_date" required autocomplete="start_date" />
                                <x-input-error for="start_date" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="end_date" value="{{ __('End Date') }}" />
                                <x-input id="end_date" type="text" class="mt-1 block w-full min-h-[40px]"
                                    wire:model.live="state.end_date" required autocomplete="end_date" />
                                <x-input-error for="end_date" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="tipe_kontrak" value="{{ __('Tipe Kontrak') }}" />
                                <x-input id="tipe_kontrak" type="text" class="mt-1 block w-full min-h-[40px]"
                                    wire:model.live="state.tipe_kontrak" required autocomplete="tipe_kontrak" />
                                <x-input-error for="tipe_kontrak" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="path" value="{{ __('Path') }}" />
                                <x-input id="path" type="text" class="mt-1 block w-full min-h-[40px]"
                                    wire:model.live="state.path" required autocomplete="path" />
                                <x-input-error for="path" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="tipe_pembayaran" value="{{ __('Tipe Pembayaran') }}" />
                                <x-input id="tipe_pembayaran" type="text" class="mt-1 block w-full min-h-[40px]"
                                    wire:model.live="state.tipe_pembayaran" required autocomplete="tipe_pembayaran" />
                                <x-input-error for="tipe_pembayaran" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="alamat" value="{{ __('Alamat') }}" />
                                <x-input id="alamat" type="text" class="mt-1 block w-full min-h-[40px]"
                                    wire:model.live="state.alamat" required autocomplete="alamat" />
                                <x-input-error for="alamat" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="unit_kerja" value="{{ __('Unit Kerja') }}" />
                                <x-input id="unit_kerja" type="text" class="mt-1 block w-full min-h-[40px]"
                                    wire:model.live="state.unit_kerja" required autocomplete="unit_kerja" />
                                <x-input-error for="unit_kerja" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="status" value="{{ __('Status') }}" />
                                <x-input id="status" type="text" class="mt-1 block w-full min-h-[40px]"
                                    wire:model.live="state.status" required autocomplete="status" />
                                <x-input-error for="status" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-gray-700/20 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                            <x-secondary-button
                                onclick="window.location='{{ route('contracts.index') }}'">Batal</x-secondary-button>
                            <x-secondary-button
                                onclick="window.location='{{ route('detailManagementFee') }}'">Simpan</x-secondary-button>
                        </div>
                    </div>
                </form>
            </div>
            {{-- Tambah Data Baru End --}}
        </div>
    </div>
</x-app-layout>
