<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Notification Details</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                {{ $notification->data['title'] ?? 'No Title' }}
            </h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ $notification->data['message'] ?? 'No Message' }}
            </p>
            <p class="text-gray-500 dark:text-gray-400 text-sm">
                Received {{ $notification->created_at->diffForHumans() }}
            </p>
        </div>

        <div class="mt-5">
            <x-secondary-button onclick="window.location='{{ route('notifications.index') }}'">
                Back
            </x-secondary-button>
        </div>
    </div>
</x-app-layout>