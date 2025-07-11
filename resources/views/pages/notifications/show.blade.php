<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">📩 Pesan</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
            <!-- Header Notifikasi -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                        Dikirim oleh :
                        <span class="text-sm ml-1">
                            {{ $notification->sender->name }} ({{ $notification->sender->nip }} -
                            {{ $notification->sender->position }})
                        </span>
                    </h3>

                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                        Nomor Invoice :
                        <span class="text-sm ml-1">
                            {{ $notification->invoice_number }}
                        </span>
                    </h3>
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Tanggal dikirim : {{ $notification->created_at->format('d M Y, H:i') }}
                </span>
            </div>

            <!-- Isi Notifikasi -->
            @php
                $textMessage = $notification->messages ?? 'Tidak ada pesan';
                preg_match('/https?:\/\/[^\s]+/', $textMessage, $matches);
                $url = $matches[0] ?? null;

                if ($url) {
                    $textMessage = str_replace($url, '', $textMessage);
                }
            @endphp
            <div class="w-full border-b border-gray-300 dark:border-gray-600 my-3"></div>

            <p class="text-sm font-semibold text-gray-800 dark:text-gray-300">
                Pesan :
                <span class="font-normal">
                    {{ trim($textMessage) }}
                </span>
            </p>

            @if ($url)
                <x-button-action color="violet" @click="window.open('{{ $url }}', '_blank')"
                    class="px-2 my-3 text-xs w-28">
                    Lihat Detail >>
                </x-button-action>
            @endif

            <!-- FOOTER CARD: Grid Layout -->
            <div class="grid grid-cols-2 items-center mt-5">
                @if (!empty($notification->data['document_id']))
                    <div>
                        <x-button-action color="blue" icon="eye"
                            href="{{ route('non-management-fee.show', $notification->data['document_id']) }}">
                            Lihat Dokumen
                        </x-button-action>
                    </div>
                @endif

            </div>
            <!-- KANAN BAWAH: Tombol Kembali & Hapus -->
            <div class="flex justify-end gap-2 ml-auto">
                <x-secondary-button onclick="window.location='{{ route('notifications.index') }}'">
                    Kembali
                </x-secondary-button>
                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                    @csrf
                    @method('DELETE')
                    <x-button-action color="red" icon="trash">
                        Hapus
                    </x-button-action>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
