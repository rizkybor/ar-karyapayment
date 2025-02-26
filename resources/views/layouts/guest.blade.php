<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />

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
</head>

<body class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400">

    <main class="bg-white dark:bg-gray-900">

        <!-- Content -->
        <div class="w-full">

            <div class="min-h-[100dvh] h-full">

                <!-- Header -->
                <div>
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                        <!-- Logo -->
                        <a class="block" href="{{ route('dashboard') }}">
                            {{-- <svg class="fill-violet-500" xmlns="http://www.w3.org/2000/svg" width="32" height="32">
                                    <path d="M31.956 14.8C31.372 6.92 25.08.628 17.2.044V5.76a9.04 9.04 0 0 0 9.04 9.04h5.716ZM14.8 26.24v5.716C6.92 31.372.63 25.08.044 17.2H5.76a9.04 9.04 0 0 1 9.04 9.04Zm11.44-9.04h5.716c-.584 7.88-6.876 14.172-14.756 14.756V26.24a9.04 9.04 0 0 1 9.04-9.04ZM.044 14.8C.63 6.92 6.92.628 14.8.044V5.76a9.04 9.04 0 0 1-9.04 9.04H.044Z" />
                                </svg> --}}
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="62" height="62" viewBox="0 0 216 216">
                                <image
                                    xlink:href="data:image/png;base64,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"
                                    x="0" y="0" width="216" height="216" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="w-full max-w-3xl mx-auto px-4 py-8">
                    {{ $slot }}
                </div>

            </div>

        </div>

        </div>

    </main>

    @livewireScriptConfig
</body>

</html>
