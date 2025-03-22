<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Karya-Invoice') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />


    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <script>
        if (localStorage.getItem('dark-mode') === 'false' || !('dark-mode' in localStorage)) {
            document.querySelector('html').classList.remove('dark');
            document.querySelector('html').style.colorScheme = 'light';
        } else {
            document.querySelector('html').classList.add('dark');
            document.querySelector('html').style.colorScheme = 'dark';
        }
    </script>

    <style>
        .dataTables_paginate {
            display: none !important;
        }

        @media (max-width: 640px) {
            #tablePagination {
                justify-content: center !important;
                margin-top: 5px;
            }

            #tableInfo {
                text-align: center;
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400"
    :class="{ 'sidebar-expanded': sidebarExpanded }" x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' }" x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))">

    <script>
        if (localStorage.getItem('sidebar-expanded') == 'true') {
            document.querySelector('body').classList.add('sidebar-expanded');
        } else {
            document.querySelector('body').classList.remove('sidebar-expanded');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Page wrapper -->
    <div class="flex h-[100dvh] overflow-hidden">

        <x-app.sidebar :variant="$attributes['sidebarVariant']" />

        <!-- Content area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden @if ($attributes['background']) {{ $attributes['background'] }} @endif"
            x-ref="contentarea">

            <x-app.header :variant="$attributes['headerVariant']" />

            <main class="grow">
                {{ $slot }}
                <x-modal.global.modal-confirmation-global />
                <x-modal.global.modal-alert-global id="globalAlertModal" />
            </main>

        </div>

    </div>

    @livewireScriptConfig

</body>

<script>
    function openConfirmationModal(title, description, yesCallback) {
        const modal = document.getElementById('globalConfirmationModal');
        const modalTitle = modal?.querySelector('h3');
        const modalDesc = modal?.querySelector('p');
        const yesBtn = document.getElementById('globalYesButton');

        if (!modal || !yesBtn) {
            console.error("Modal atau tombol Ya tidak ditemukan!");
            return;
        }

        // Set teks
        modalTitle.textContent = title || 'Konfirmasi';
        modalDesc.textContent = description || 'Apakah Anda yakin?';

        // Bersihkan event lama
        const newYesBtn = yesBtn.cloneNode(true);
        yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);

        newYesBtn.addEventListener('click', function() {
            yesCallback();
            modal.classList.add('hidden');
        });

        modal.classList.remove('hidden');
    }
</script>
</html>
