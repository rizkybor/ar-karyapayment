<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'btn bg-yellow-500 dark:bg-yellow-600 border-yellow-600 dark:border-yellow-700 hover:bg-yellow-600 dark:hover:bg-yellow-700 text-white']) }}>
    {{ $slot }}
</button>
