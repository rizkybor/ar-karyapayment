<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Detail Transaksi</h1>
            </div>
        </div>
        <form action="{{ route('management-non-fee.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mt-5 mb-5 md:mt-0 md:col-span-2">
                <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-label for="type" value="{{ __('Kontrak') }}" />
                           
                        </div>
                        <div>
                            <x-label for="letter_number" value="{{ __('No Surat') }}" />
                        </div>
                        <div>
                            <x-label for="invoice_number" value="{{ __('No Invoice') }}" />
                          
                        </div>
                        <div>
                            <x-label for="period" value="{{ __('Periode / Termin') }}" />
                           
                        </div>
                        <div>
                            <x-label for="receipt_number" value="{{ __('No Kwitansi') }}" />
                           
                        </div>
                        <div class="sm:row-span-2">
                            <x-label for="letter_subject" value="{{ __('Perihal Surat') }}" />
                         
                        </div>
                        <div>
                            <x-label for="employee_name" value="{{ __('Nama Pemberi Kerja') }}" />
                           
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="form-group">
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <x-secondary-button onclick="window.location='{{ route('management-fee.index') }}'">
                        Batal
                    </x-secondary-button>
                    <x-button type="submit">Simpan</x-button>
                </div>
            </div> --}}
        </form>
    </div>
    <script>
      
    </script>
</x-app-layout>
