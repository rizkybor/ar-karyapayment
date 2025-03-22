<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">📩 Semua Pesan</h1>

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
                    <x-button-action color="red" icon="trash" type="button" onclick="clearAllNotifications()">
                        Hapus Semua
                    </x-button-action>
                @endif
            </div>
        </div>

        <!-- Daftar Notifikasi -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5">
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
                                    $url = $notification;
                                    $messageParts = explode('Lihat detail:', $message, 2);
                                    $textMessage = trim($messageParts[0]);
                                    $url = url('/notifications/' . $notification->id);
                                } else {
                                    $textMessage = is_string($message) ? $message : 'Tidak ada pesan.';
                                    $url = null;
                                }
                            @endphp

                            <div class="relative p-4 border rounded-lg cursor-pointer transition-colors duration-300 ease-in-out 
                                {{ $notification->read_at === null ? 'bg-yellow-100 dark:bg-yellow-900' : 'bg-gray-200 dark:bg-gray-700' }}"
                                @click="markAsRead('{{ route('notifications.markAsRead', $notification->id) }}', {{ $notification->id }}, '{{ $url }}');"
                                data-id="{{ $notification->id }}">

                                <div class="flex justify-between items-center">

                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-300">
                                        Dikirim oleh :
                                        <span class="font-normal">
                                            ({{ $notification->sender->nip }})
                                            {{ $notification->sender->name }} - {{ $notification->sender->position }}
                                        </span>
                                    </p>

                                    <p class="text-sm text-gray-800 dark:text-gray-300">
                                        Tanggal dikirim : {{ $notification->created_at }}
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

                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>

                                    <!-- 📌 Kanan: Tombol Aksi -->
                                    <div class="w-1/3 flex justify-end gap-2">
                                        <x-button-action color="red" type="button"
                                            @click.stop="deleteNotification({{ $notification->id }})">
                                            Hapus
                                        </x-button-action>
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
    function markAsRead(url, notificationId, redirectUrl) {
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
                if (data.success) {
                    setTimeout(() => {
                        location.reload(); // 🔄 REFRESH halaman setelah notifikasi ditandai sebagai dibaca
                    }, 500);
                    window.location.href = redirectUrl;
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function deleteNotification(notificationId) {
        openConfirmationModal(
            "Konfirmasi Hapus",
            "Apakah Anda yakin ingin menghapus notifikasi ini?",
            function() {
                console.log(notificationId, '<< cek')
                fetch(`{{ url('/notifications') }}/${notificationId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal menghapus notifikasi');
                        return response.json();
                    })
                    .then(() => location.reload())
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus notifikasi.');
                    });
            }
        );
    }

    function clearAllNotifications() {
        openConfirmationModal(
            "Konfirmasi Hapus Semua",
            "Apakah Anda yakin ingin menghapus semua notifikasi?",
            function() {
                fetch(`{{ route('notifications.clearAll') }}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal menghapus semua notifikasi');
                        return response.json();
                    })
                    .then(() => location.reload())
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus semua notifikasi.');
                    });
            }
        );
    }
</script>
