<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Notifikasi</h1>

            <div class="flex gap-2">
                @if ($unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                        @csrf
                        <x-button-action color="green" icon="check" type="submit">
                            Tandai Semua Dibaca
                        </x-button-action>
                    </form>
                @endif

                @if ($unreadNotifications->count() > 0 || $readNotifications->count() > 0)
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

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5 overflow-x-auto">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">Notifikasi Baru</h2>
            @if ($unreadNotifications->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">Tidak ada notifikasi baru.</p>
            @else
                <table class="table-auto w-full min-w-[600px]">
                    <thead>
                        <tr class="text-gray-400 dark:text-gray-500 uppercase text-xs border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left p-3">Pesan</th>
                            <th class="text-left p-3">Tanggal</th>
                            <th class="text-center p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($unreadNotifications as $notification)
                            <tr class="border-b border-gray-100 dark:border-gray-700 text-black dark:text-white font-semibold">
                                <td class="p-3">{{ $notification->data['message'] ?? 'Tidak ada pesan' }}</td>
                                <td class="p-3">{{ $notification->created_at->diffForHumans() }}</td>
                                <td class="p-3 text-center flex justify-center gap-2">
                                    <x-button-action color="blue" icon="eye" href="{{ route('notifications.show', $notification->id) }}">
                                        Lihat
                                    </x-button-action>
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-button-action color="red" icon="trash">
                                            Hapus
                                        </x-button-action>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5 mt-6 overflow-x-auto">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">Notifikasi Lama</h2>
            @if ($readNotifications->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">Tidak ada notifikasi lama.</p>
            @else
                <table class="table-auto w-full min-w-[600px]">
                    <thead>
                        <tr class="text-gray-400 dark:text-gray-500 uppercase text-xs border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left p-3">Pesan</th>
                            <th class="text-left p-3">Tanggal</th>
                            <th class="text-center p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($readNotifications as $notification)
                            <tr class="border-b border-gray-100 dark:border-gray-700 text-gray-500 dark:text-gray-400">
                                <td class="p-3">{{ $notification->data['message'] ?? 'Tidak ada pesan' }}</td>
                                <td class="p-3">{{ $notification->created_at->diffForHumans() }}</td>
                                <td class="p-3 text-center flex justify-center gap-2">
                                    <x-button-action color="blue" icon="eye" href="{{ route('notifications.show', $notification->id) }}">
                                        Lihat
                                    </x-button-action>
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-button-action color="red" icon="trash">
                                            Hapus
                                        </x-button-action>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>