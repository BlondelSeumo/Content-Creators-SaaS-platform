<?php

return [
    'name' => 'LaravelPWA',
    'manifest' => [
        'name' => env('APP_NAME', 'PWA App'),
        'short_name' => 'PWA',
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#505050',
        'display' => 'standalone',
        'orientation'=> 'any',
        'status_bar'=> 'black',
        'icons' => [
            '192x192' => [
                'path' => '/img/pwa/manifest-icon-192.maskable.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/img/pwa/manifest-icon-512.maskable.png',
                'purpose' => 'any'
            ],

        ],
        'splash' => [
            '640x1136' => '/img/pwa/apple-splash-640-1136.jpg',
            '750x1334' => '/img/pwa/apple-splash-750-1334.jpg',
            '828x1792' => '/img/pwa/apple-splash-828-1792.jpg',
            '1125x2436' => '/img/pwa/apple-splash-1125-2436.jpg',
            '1242x2208' => '/img/pwa/apple-splash-1242-2208.jpg',
            '1242x2688' => '/img/pwa/apple-splash-1242-2688.jpg',
            '1536x2048' => '/img/pwa/apple-splash-1536-2048.jpg',
            '1668x2224' => '/img/pwa/apple-splash-2224-1668.jpg',
            '1668x2388' => '/img/pwa/apple-splash-2388-1668.jpg',
            '2048x2732' => '/img/pwa/apple-splash-2732-2048.jpg',
        ],
        'custom' => []
    ]
];
