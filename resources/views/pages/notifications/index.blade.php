<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Notifikasi</h1>

            <div class="flex gap-2">
                @if ($notifications->whereNull('read_at')->count() > 0)
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                        @csrf
                        <x-button-action color="green" icon="check" type="submit">
                            Tandai Semua Dibaca
                        </x-button-action>
                    </form>
                @endif

                @if ($notifications->count() > 0)
                    <form action="{{ route('notifications.clearAll') }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua notifikasi?');">
                        @csrf
                        @method('DELETE')
                        <x-button-action color="red" icon="trash" type="submit">
                            Hapus Semua
                        </x-button-action>
                    </form>
                @endif
            </div>
        </div>

        <!-- Daftar Notifikasi -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">📩 Semua Notifikasi</h2>

            <div class="overflow-y-auto max-h-[600px] scrollbar-hidden">
                @if ($notifications->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada notifikasi.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($notifications as $notification)
                            @php
                                $rawMessage = $notification->messages ?? 'Tidak ada pesan';

                                // Coba decode jika formatnya JSON
                                if (is_string($rawMessage) && json_decode($rawMessage, true)) {
                                    $message = json_decode($rawMessage, true);
                                } else {
                                    $message = $rawMessage;
                                }

                                // Jika berbentuk string
                                if (is_string($message) && str_contains($message, 'Lihat detail:')) {
                                    $messageParts = explode('Lihat detail:', $message, 2);
                                    $textMessage = trim($messageParts[0]); // Pesan utama
                                    $url = isset($messageParts[1]) ? trim($messageParts[1]) : null; // Ambil URL

                                    // Validasi URL
                                    if ($url && !filter_var($url, FILTER_VALIDATE_URL)) {
                                        $url = null; // Kosongkan jika tidak valid
                                    }
                                } else {
                                    $textMessage = is_string($message) ? $message : 'Tidak ada pesan.';
                                    $url = null;
                                }
                            @endphp

                            <div class="relative p-4 border rounded-lg cursor-pointer transition-colors duration-300 ease-in-out 
                                {{ $notification->read_at === null ? 'bg-yellow-100 dark:bg-yellow-900' : 'bg-gray-200 dark:bg-gray-700' }}"
                                @click="markAsRead('{{ route('notifications.markAsRead', $notification->id) }}', {{ $notification->id }});"
                                data-id="{{ $notification->id }}">

                                <div class="flex justify-between items-center">

                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-300">
                                    Pengirim : 
                                    <span class="font-normal">
                                        ({{ $notification->sender->nip }}) {{ $notification->sender->name }} - {{ $notification->sender->position }}
                                    </span>
                                </p>

                                <p class="text-sm text-gray-800 dark:text-gray-300">
                                    {{ $notification->created_at }}
                                </p>
                                </div>
                                <div class="w-full border-b border-gray-300 dark:border-gray-600 my-3"></div>

                                <div class="flex justify-between items-center">
                                    <!-- 📌 Kiri: Pesan & Tautan -->
                                    <div class="w-2/3">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-300 mt-1">
                                            Pesan :
                                        </p>
                                        <p class="text-sm text-gray-800 dark:text-gray-200">
                                            {{ $textMessage }}
                                        </p>

                                        @if ($url)
                                            <x-button-action color="violet"
                                                @click="window.open('{{ $url }}', '_blank')"
                                                class="px-2 my-3 text-xs w-28">
                                                Lihat Detail >>
                                            </x-button-action>
                                        @endif
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>

                                    <!-- 📌 Kanan: Tombol Aksi -->
                                    <div class="w-1/3 flex justify-end gap-2">
                                        <form action="{{ route('notifications.destroy', $notification->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-button-action color="red">
                                                Hapus
                                            </x-button-action>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function markAsRead(url, notificationId) {
        fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json(); // 🔥 Pastikan hanya parse jika JSON
            })
            .then(data => {
                console.log('Response dari server:', data);
                if (data.success) {
                    setTimeout(() => {
                        location.reload(); // 🔄 REFRESH halaman setelah notifikasi ditandai sebagai dibaca
                    }, 500);
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
