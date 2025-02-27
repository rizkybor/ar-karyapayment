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
        /* Wrapper untuk DataTables agar tidak overflow */
        .dataTables_wrapper {
            margin-top: 0 !important;
            overflow-x: auto;
        }

        /* Styling Pagination agar lebih elegan */
        .dataTables_paginate {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
        }

        .dataTables_paginate .paginate_button {
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 500;
            transition: all 0.3s ease-in-out;
        }

        .dataTables_paginate .paginate_button:hover {
            background-color: #e5e7eb;
        }

        .dataTables_paginate .paginate_button.current {
            background-color: #6366f1;
            color: white;
            border-color: #6366f1;
        }

        .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Responsif: Stack pagination di layar kecil */
        @media (max-width: 768px) {
            .dataTables_paginate {
                justify-content: center;
                flex-wrap: wrap;
            }
        }

        /* Styling untuk search bar dan dropdown */
        .custom-controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .custom-search {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .custom-search input {
            border: 1px solid #d1d5db;
            padding: 6px 12px;
            border-radius: 6px;
        }

        /* Responsif: Search dan dropdown stack di layar kecil */
        @media (max-width: 640px) {
            .custom-controls {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
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
            </main>

        </div>

    </div>

    @livewireScriptConfig

</body>

</html>
