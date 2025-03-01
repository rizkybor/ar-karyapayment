@props(['color' => 'gray', 'icon' => null])

@php
    $colors = [
        'gray' =>
            'bg-gray-500 hover:bg-gray-600 focus:ring-gray-300 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-gray-900',
        'violet' =>
            'bg-violet-500 hover:bg-violet-600 focus:ring-violet-300 dark:bg-violet-700 dark:hover:bg-violet-800 dark:focus:ring-violet-900',
    ];

    $colorClass = $colors[$color] ?? $colors['gray'];

@endphp

<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => " items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg text-white $colorClass focus:outline-none focus:ring-2 whitespace-nowrap",
    ]) }}>
    {{ $slot }}
</button>
