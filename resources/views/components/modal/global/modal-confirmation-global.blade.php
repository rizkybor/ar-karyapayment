@props([
    'id' => 'globalConfirmationModal',
    'title' => 'Konfirmasi',
    'description' => 'Apakah Anda yakin?',
    'yesLabel' => 'Ya',
    'noLabel' => 'Tidak',
    'yesAction' => null,
])

<div id="{{ $id }}" class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center px-4 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md sm:max-h-[90vh] sm:overflow-auto flex flex-col">
        <!-- Header -->
        <div class="flex justify-center items-center mb-2">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ $title }}
            </h3>
        </div>

        <!-- Deskripsi -->
        <div class="flex justify-center items-center mb-6">
            <p class="text-gray-700 dark:text-gray-300 text-sm text-center">
                {{ $description }}
            </p>
        </div>

        <!-- Tombol -->
        <div class="flex justify-center gap-4">
            <x-secondary-button type="button" onclick="location.reload(true)">
                {{ $noLabel }}
            </x-secondary-button>
            <x-button-action color="green" type="button" onclick="{{ $yesAction }}()">
                {{ $yesLabel }}
            </x-button-action>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
</script>