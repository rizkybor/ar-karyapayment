@props([
    'id' => 'globalAlertModal',
    'type' => 'success',
    'title' => 'Berhasil!',
    'message' => 'Data berhasil disimpan.',
    'timeout' => 3000,
])

@php
    $typeColors = [
        'success' => 'text-green-800 border-green-300',
        'error' => 'text-red-800 border-red-300',
        'warning' => 'text-yellow-800 border-yellow-300',
        'info' => 'text-blue-800 border-blue-300',
    ];

    $colorClass = $typeColors[$type] ?? $typeColors['success'];
@endphp

<div id="{{ $id }}"
    class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center px-4 hidden">
    <div id="{{ $id }}Card"
        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md sm:max-h-[90vh] sm:overflow-auto flex flex-col border {{ $colorClass }}">
        <!-- Title -->
        <div class="flex justify-center items-center mb-2">
            <h3 id="{{ $id }}Title" class="text-lg font-semibold">
                {{ $title }}
            </h3>
        </div>

        <!-- Message -->
        <div class="flex justify-center items-center mb-2">
            <p id="{{ $id }}Message" class="text-gray-700 dark:text-gray-300 text-sm text-center">
                {{ $message }}
            </p>
        </div>
    </div>
</div>

<script>
    function showAutoCloseAlert(id = 'globalAlertModal', timeout = 3000, message = null, type = 'success', title =
        null) {
        const modal = document.getElementById(id);
        const titleEl = document.getElementById(id + 'Title');
        const messageEl = document.getElementById(id + 'Message');
        const cardEl = document.getElementById(id + 'Card');

        if (!modal) return;

        if (messageEl && message) {
            messageEl.innerText = message;
        }

        if (titleEl && title) {
            titleEl.innerText = title;
        }

        const colorMap = {
            success: 'text-green-800 border-green-300',
            error: 'text-red-800 border-red-300',
            warning: 'text-yellow-800 border-yellow-300',
            info: 'text-blue-800 border-blue-300'
        };

        if (cardEl) {
            cardEl.className =
                `bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md sm:max-h-[90vh] sm:overflow-auto flex flex-col border ${colorMap[type] || colorMap.success}`;
        }

        modal.classList.remove('hidden');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, timeout);
    }
</script>
