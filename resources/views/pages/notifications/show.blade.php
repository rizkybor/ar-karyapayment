<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">📩 Detail Notifikasi</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
            <!-- Header Notifikasi -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    {!! getNotificationIcon($notification->data['status'] ?? 'info') !!}
                    {{ $notification->data['title'] ?? 'Notifikasi' }}
                </h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $notification->created_at->format('d M Y, H:i') }}
                </span>
            </div>

            <!-- Isi Notifikasi -->
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ $notification->data['message'] ?? 'Tidak ada pesan' }}
            </p>

            <!-- Tampilkan link ke dokumen jika ada -->
            @if (!empty($notification->data['document_id']))
                <div class="mb-4">
                    <x-button-action color="blue" icon="eye" href="{{ route('management-non-fee.show', $notification->data['document_id']) }}">
                        Lihat Dokumen
                    </x-button-action>
                </div>
            @endif

            <!-- Tombol Kembali -->
            <div class="mt-5 flex gap-2">
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