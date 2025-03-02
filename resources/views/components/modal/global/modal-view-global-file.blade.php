@props(['file', 'nonManfeeDocument'])

@php
    $filePath = asset('storage/' . $file->path);
    $extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
    $isPdf = $extension === 'pdf';
@endphp

<div class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 flex items-center justify-center px-4"
    x-show="modalOpen" x-cloak>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-2xl sm:max-w-md sm:h-auto sm:max-h-[90vh] sm:overflow-auto flex flex-col items-center justify-center">
        
        <!-- Header Modal -->
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 text-center">
            Lihat Lampiran
        </h3>

        <!-- Info File -->
        <p class="text-gray-700 dark:text-gray-300 mb-4 text-center">
            <strong>Nama File:</strong> {{ $file->file_name }}
        </p>

        <!-- Konten Lampiran -->
        <div class="mt-4 flex flex-col items-center justify-center w-full">
            @if ($isImage)
                <!-- Tampilkan Gambar -->
                <img src="{{ $filePath }}" alt="Lampiran" class="max-w-full max-h-[60vh] rounded-lg shadow mx-auto">
            @elseif ($isPdf)
                <!-- Tampilkan PDF di Desktop -->
                <div class="w-full flex items-center justify-center">
                    <embed src="{{ $filePath }}" type="application/pdf" class="w-full max-w-xl h-96 border rounded-lg shadow sm:block" />
                </div>
                <!-- Tampilkan Link PDF di Mobile -->
                <p class="sm:hidden text-sm text-gray-500 text-center">
                    <a href="{{ $filePath }}" target="_blank" class="text-blue-500 underline">Klik di sini</a> untuk melihat PDF di tab baru.
                </p>
            @else
                <!-- Tampilkan File Lain dalam Iframe -->
                <div class="w-full flex items-center justify-center">
                    <iframe src="{{ $filePath }}" class="pt-16 w-full max-w-xl h-96 border rounded-lg shadow"></iframe>
                </div>
            @endif
        </div>

        <!-- Footer Modal -->
        <div class="flex justify-center mt-4">
            <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                @click="modalOpen = false">
                Close
            </button>
        </div>
    </div>
</div>