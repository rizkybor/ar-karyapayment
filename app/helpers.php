<?php

if (!function_exists('getNotificationIcon')) {
    function getNotificationIcon($status)
    {
        $icons = [
            'approved' => '<span class="text-green-500 text-xl mr-2">✔️</span>',
            'pending' => '<span class="text-yellow-500 text-xl mr-2">⏳</span>',
            'revised' => '<span class="text-orange-500 text-xl mr-2">✍️</span>',
            'rejected' => '<span class="text-red-500 text-xl mr-2">❌</span>',
            'info' => '<span class="text-blue-500 text-xl mr-2">ℹ️</span>',
        ];

        return $icons[$status] ?? $icons['info'];
    }
}