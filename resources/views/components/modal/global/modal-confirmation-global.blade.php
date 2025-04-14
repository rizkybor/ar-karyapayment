@props([
    'id' => 'globalConfirmationModal',
    'title' => 'Konfirmasi',
    'description' => 'Apakah Anda yakin?',
    'yesLabel' => 'Ya',
    'noLabel' => 'Tidak',
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
            <x-secondary-button type="button" onclick="document.getElementById('{{ $id }}').classList.add('hidden')">
                {{ $noLabel }}
            </x-secondary-button>
            <x-button-action id="globalYesButton" color="green" type="button">
                {{ $yesLabel }}
            </x-button-action>
        </div>
    </div>
</div>

<script>
    function openConfirmationModal(title, description, yesCallback) {
        const modal = document.getElementById('globalConfirmationModal');
        const modalTitle = modal.querySelector('h3');
        const modalDesc = modal.querySelector('p');
        const yesBtn = document.getElementById('globalYesButton');

        if (!yesBtn) {
            console.error("Tombol Ya tidak ditemukan dalam modal!");
            return;
        }

        // Set teks
        modalTitle.textContent = title || 'Konfirmasi';
        modalDesc.textContent = description || 'Apakah Anda yakin?';

        // Reset event listener dulu
        const newYesBtn = yesBtn.cloneNode(true);
        yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);

        newYesBtn.addEventListener('click', function () {
            yesCallback();
            modal.classList.add('hidden');
        });

        // Tampilkan modal
        modal.classList.remove('hidden');
    }
</script>