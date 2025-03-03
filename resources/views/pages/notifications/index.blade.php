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
                            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 flex items-center justify-between">
                                <div>
                                    <p class="text-gray-800 dark:text-white font-medium flex items-center">
                                        {{ $notification->messages ?? 'Tidak ada pesan' }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <div class="flex gap-2">
                                    <x-button-action color="blue" icon="eye"
                                        href="{{ route('notifications.show', $notification->id) }}">
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