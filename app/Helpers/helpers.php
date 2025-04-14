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