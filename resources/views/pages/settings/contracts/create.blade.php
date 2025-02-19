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

                <form action="" class="max-w-3xl w-11/12 mx-auto p-6" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="grid grid-cols-1 gap-y-6">
                            <div>
                                <x-label for="contract_number" value="{{ __('Nomer Kontrak') }}" />
                                <x-input id="contract_number" type="number" name="contract_number"
                                    class="mt-1 block w-full min-h-[40px]" placeholder="Masukkan nomer kontrak"
                                    wire:model.live="state.contract_number" required autocomplete="contract_number" />
                                <x-input-error for="contract_number" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="employee_name" value="{{ __('Nama Perusahaan') }}" />
                                <x-input id="employee_name" type="text" name="employee_name"
                                    class="mt-1 block w-full min-h-[40px]" placeholder="Masukkan nama perusahaan"
                                    wire:model.live="state.employee_name" required autocomplete="employee_name" />
                                <x-input-error for="employee_name" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="value" value="{{ __('Value') }}" />
                                <x-input id="value" type="number" name="value"
                                    class="mt-1 block w-full min-h-[40px]" placeholder="Masukkan value"
                                    wire:model.live="state.value" required autocomplete="value" />
                                <x-input-error for="value" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-label for="start_date" value="{{ __('Start Date') }}" />
                                    <x-input id="start_date" type="date" name="start_date"
                                        class="mt-1 block w-full min-h-[40px]" wire:model.live="state.start_date"
                                        required autocomplete="start_date" />
                                    <x-input-error for="start_date" class="mt-2" />
                                </div>
                                <div>
                                    <x-label for="end_date" value="{{ __('End Date') }}" />
                                    <x-input id="end_date" type="date" name="end_date"
                                        class="mt-1 block w-full min-h-[40px]" wire:model.live="state.end_date" required
                                        autocomplete="end_date" />
                                    <x-input-error for="end_date" class="mt-2" />
                                </div>
                            </div>


                            <div>
                                <!-- Label -->
                                <x-label for="type" value="{{ __('Tipe Kontrak') }}" />

                                <!-- Dropdown Select -->
                                <select id="type" name="type" class="mt-1 block w-full form-select "
                                    wire:model.live="state.type">
                                    <option value="">Pilih Tipe Kontrak</option>
                                    <option value="maker">Manfee</option>
                                    <option value="kadiv">Non Manfee</option>
                                </select>
                            </div>

                            <div>
                                <x-label for="path" value="{{ __('Path') }}" />
                                <x-input id="path" type="file" name="path" class="mt-1 block w-full"
                                    wire:model.live="state.path" autocomplete="path" />
                                <x-input-error for="path" class="mt-2" />
                            </div>

                            <div>
                                <!-- Label -->
                                <x-label for="bill_type" value="{{ __('Tipe Pembayaran') }}" />

                                <!-- Dropdown Select -->
                                <select id="bill_type" name="bill_type" class="mt-1 block w-full form-select "
                                    wire:model.live="state.bill_type">
                                    <option value="">Pilih Tipe Pembayaran</option>
                                    <option value="gaji">Gaji</option>
                                    <option value="sppd">Sppd</option>
                                </select>
                            </div>

                            <div>
                                <x-label for="address" value="{{ __('Alamat') }}" />
                                <x-input-wide id="address" type="text" name=""
                                    class="mt-1 block w-full min-h-[40px]" placeholder="Masukkan alamat"
                                    wire:model.live="state.address" required autocomplete="address" />
                                <x-input-error for="address" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="work_unit" value="{{ __('Unit Kerja') }}" />
                                <x-input id="work_unit" type="text" name="work_unit"
                                    class="mt-1 block w-full min-h-[40px]" placeholder="Masukkan unit kerja"
                                    wire:model.live="state.work_unit" required autocomplete="work_unit" />
                                <x-input-error for="work_unit" class="mt-2" />
                            </div>

                            <div>
                                <!-- Label -->
                                <x-label for="status" value="{{ __('Status') }}" />

                                <!-- Dropdown Select -->
                                <select id="status" name="status" class="mt-1 block w-full form-select "
                                    wire:model.live="state.status">
                                    <option value="">Pilih Status</option>
                                    <option value="true">True</option>
                                    <option value="false">False</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-gray-700/20 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                            <x-secondary-button
                                onclick="window.location='{{ route('contracts.index') }}'">Batal</x-secondary-button>
                            <div class="form-group">
                                <x-button type="submit">Simpan</x-button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            {{-- Tambah Data Baru End --}}
        </div>
    </div>
</x-app-layout>
