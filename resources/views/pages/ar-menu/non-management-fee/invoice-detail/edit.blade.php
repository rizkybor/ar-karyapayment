<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                {{-- <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Edit Detail Invoice
                    #{{ $nonManfeeDocument['invoice_number'] }}
                </h1>
                <x-label class="text-sm mt-1">Nama Kontrak : {{ $nonManfeeDocument->contract->title }} ( {{ $nonManfeeDocument->contract->employee_name }} ) </x-label>
                <x-label class="text-sm mt-1">Dibuat oleh : {{ $nonManfeeDocument->creator->name ?? '-' }} ({{ $nonManfeeDocument->creator->department ?? '-' }}) </x-label> --}}

                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Edit Invoice

                </h1>
                <h1 class="text-l md:text-l text-gray-800 dark:text-gray-100 font-bold">Nama Kontrak :
                    {{ $nonManfeeDocument->contract->title }} (
                    {{ $nonManfeeDocument->contract->employee_name }} ) </h1>
                <h1 class="text-l md:text-l text-gray-800 dark:text-gray-100 font-bold">Nomor
                    : {{ $nonManfeeDocument['invoice_number'] }}
                </h1>
            

                 {{-- Inline Editable Periode --}}
                <div x-data="{ editPeriod: false, period: @js($nonManfeeDocument->period) }" class="mt-2">
                    <template x-if="!editPeriod">
                        <h1 class="text-m md:text-m text-gray-800 dark:text-gray-100 font-bold flex items-center">
                            Periode: <span class="ml-1" x-text="period || '-'"></span>

                            @if (auth()->user()->hasRole('maker'))
                                <button class="group ml-2" @click="editPeriod = true" title="Edit Periode">
                                    <svg width="18px" height="18px" viewBox="0 0 24.00 24.00" fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="transform transition-transform duration-200 ease-in-out group-hover:scale-125">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path
                                                d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005Z"
                                                stroke="#755ff8" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13"
                                                stroke="#755ff8" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </g>
                                    </svg>
                                </button>
                            @endif
                        </h1>
                    </template>
                    <template x-if="editPeriod">
                        <form method="POST"
                            action="{{ route('non-management-fee.periodUpdate', $nonManfeeDocument->id) }}"
                            class="flex items-center gap-2 mt-2">
                            @csrf
                            @method('PUT')
                            <input type="text" name="period" x-model="period"
                                class="form-input rounded-md shadow-sm w-full dark:bg-gray-700 dark:text-white"
                                >
                            <button type="submit" class="group ml-2" title="Simpan Period">
                                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="transform transition-transform duration-200 ease-in-out group-hover:scale-125">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path d="M8.5 12.5L10.5 14.5L15.5 9.5" stroke="#0d9488" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C21.5093 4.43821 21.8356 5.80655 21.9449 8"
                                            stroke="#0d9488" stroke-width="1.5" stroke-linecap="round" />
                                    </g>
                                </svg>
                            </button>
                        </form>
                    </template>
                </div>

                {{-- Inline Editable Perihal --}}
                <div x-data="{ editSubject: false, subject: @js($nonManfeeDocument->letter_subject) }" class="mt-2">
                    <template x-if="!editSubject">
                        <h1 class="text-m md:text-m text-gray-800 dark:text-gray-100 font-bold flex items-center">
                            Perihal: <span class="ml-1" x-text="subject"></span>

                            @if (auth()->user()->hasRole('maker'))
                                <button class="group ml-2" @click="editSubject = true" title="Edit Perihal">
                                    <svg width="18px" height="18px" viewBox="0 0 24.00 24.00" fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="transform transition-transform duration-200 ease-in-out group-hover:scale-125">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path
                                                d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005Z"
                                                stroke="#755ff8" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13"
                                                stroke="#755ff8" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </g>
                                    </svg>
                                </button>
                            @endif
                        </h1>
                    </template>
                    <template x-if="editSubject">
                        <form method="POST"
                            action="{{ route('non-management-fee.perihalUpdate', $nonManfeeDocument->id) }}"
                            class="flex items-center gap-2 mt-2">
                            @csrf
                            @method('PUT')
                            <input type="text" name="letter_subject" x-model="subject"
                                class="form-input rounded-md shadow-sm w-full dark:bg-gray-700 dark:text-white"
                                required>
                            <button type="submit" class="group ml-2" title="Simpan Perihal">
                                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="transform transition-transform duration-200 ease-in-out group-hover:scale-125">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path d="M8.5 12.5L10.5 14.5L15.5 9.5" stroke="#0d9488" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C21.5093 4.43821 21.8356 5.80655 21.9449 8"
                                            stroke="#0d9488" stroke-width="1.5" stroke-linecap="round" />
                                    </g>
                                </svg>
                            </button>
                        </form>
                    </template>
                </div>

                {{-- Inline Editable Referensi Dokumen --}}
                <div x-data="{ editRef: false, ref: @js($nonManfeeDocument->reference_document) }" class="mt-2">
                    <template x-if="!editRef">
                        <h1 class="text-m md:text-m text-gray-800 dark:text-gray-100 font-bold flex items-center">
                            Referensi Dokumen: <span class="ml-1" x-text="ref || '-'"></span>
                            @if (auth()->user()->hasRole('maker'))
                                <button class="group ml-2" @click="editRef = true" title="Edit Referensi">
                                    <svg width="18px" height="18px" viewBox="0 0 24.00 24.00" fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="transform transition-transform duration-200 ease-in-out group-hover:scale-125">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path
                                                d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005Z"
                                                stroke="#755ff8" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13"
                                                stroke="#755ff8" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </g>
                                    </svg>
                                </button>
                            @endif
                        </h1>
                    </template>
                    <template x-if="editRef">
                        <form method="POST"
                            action="{{ route('non-management-fee.referenceUpdate', $nonManfeeDocument->id) }}"
                            class="flex items-center gap-2 mt-2">
                            @csrf
                            @method('PUT')
                            <input type="text" name="reference_document" x-model="ref"
                                class="form-input rounded-md shadow-sm w-full dark:bg-gray-700 dark:text-white">
                            <button type="submit" class="group ml-2" title="Simpan Referensi">
                                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="transform transition-transform duration-200 ease-in-out group-hover:scale-125">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path d="M8.5 12.5L10.5 14.5L15.5 9.5" stroke="#0d9488" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C21.5093 4.43821 21.8356 5.80655 21.9449 8"
                                            stroke="#0d9488" stroke-width="1.5" stroke-linecap="round" />
                                    </g>
                                </svg>
                            </button>
                        </form>
                    </template>
                </div>

                <x-label class="text-sm mt-1">Dibuat oleh : {{ $nonManfeeDocument->creator->name ?? '-' }}
                    ({{ $nonManfeeDocument->creator->department ?? '-' }}) </x-label>

            </div>
            <div class="form-group">
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <x-secondary-button onclick="window.location='{{ route('non-management-fee.index') }}'">
                        Kembali
                    </x-secondary-button>
                </div>
            </div>
        </div>

        <div class="border border-white-300 dark:border-white-700 my-6"></div>

        {{-- HEADER --}}
        <x-non-management-fee.header :transaction_status="$nonManfeeDocument['is_active']" :document="$nonManfeeDocument" :bankAccounts="$allBankAccounts" :document_status="$nonManfeeDocument['status']"
            :payment_status="$payment_status" isEditable="true" />

        <div class="grid grid-cols-1 gap-6 mt-6">
            @if ($nonManfeeDocument->status == 0 || $nonManfeeDocument->status == 102)
                {{-- DETAIL BIAYA --}}
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-non-management-fee.detail-biaya.edit :nonManfeeDocument="$nonManfeeDocument" :jenis_biaya="$jenis_biaya" :account_detailbiaya="$account_detailbiaya" />
                </div>

                {{-- AKUMULASI BIAYA --}}
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-non-management-fee.accumulated-costs.edit :nonManfeeDocument="$nonManfeeDocument" :optionAccount="$optionAccount"
                        :isEdit="false" />
                </div>

                {{-- LAMPIRAN --}}
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-non-management-fee.attachments.edit :nonManfeeDocument="$nonManfeeDocument" />
                </div>

                {{-- DESKRIPSI --}}
                {{-- <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-non-management-fee.descriptions.edit :nonManfeeDocument="$nonManfeeDocument" />
                </div> --}}
            @elseif ($nonManfeeDocument->status == 6 && auth()->user()->hasRole('perbendaharaan'))
                {{-- LAMPIRAN khusus role perbendaharaan di status 6 --}}
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-non-management-fee.attachments.edit :nonManfeeDocument="$nonManfeeDocument" />
                </div>
            @endif

            {{-- FAKTUR PAJAK --}}
            @role('pajak')
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4">
                    <x-non-management-fee.tax-files.edit :nonManfeeDocument="$nonManfeeDocument" />
                </div>
            @endrole
        </div>

    </div>
</x-app-layout>
