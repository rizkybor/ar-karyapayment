<?php

if (!function_exists('getNotificationIcon')) {
    function getNotificationIcon($type)
    {
        $icons = [
            'info'     => 'ℹ️',
            'warning'  => '⚠️',
            'success'  => '✅',
            'error'    => '❌',
            'message'  => '📩',
            'default'  => '📢',
        ];

        return $icons[$type] ?? $icons['default'];
    }
}

if (!function_exists('privy_base_url')) {
    function privy_base_url()
    {
        return config('services.privy.env') === 'production'
            ? config('services.privy.production_url')
            : config('services.privy.staging_url');
    }
}