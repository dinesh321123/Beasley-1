<?php
/**
 * App configuration file.
 */
return [
    'namespace' => 'VimeoVideoSelector',
    'author' => 'Vimeo Video <wpadmin@vvs.com>',
    'type' => 'plugin',
    'paths' => [

        'base'          => __DIR__ . '/../',
        'controllers'   => __DIR__ . '/../Controllers/',
        'views'         => __DIR__ . '/../../assets/views/',
        'lang'          => __DIR__ . '/../../assets/lang/'
    ],
    'version' => '1.0.1.2',
    'autoenqueue' => [
        // Enables or disables auto-enqueue of assets
        'enabled' => false,
        // Assets to auto-enqueue
        'assets'  => [
            [
                'asset'  => 'css/app.css',
                'dep'    => [],
                'footer' => false,
            ],
            [
                'asset'  => 'js/app.js',
                'dep'    => [],
                'footer' => true,
            ],
        ],
    ],
    'localize' => [
        // Enables or disables localization
        'enabled'    => false,
        // Default path for language files
        'path'       => __DIR__ . '/../../assets/lang/',
        // Text domain
        'textdomain' => 'vvs',
        // Unload loaded locale files before localization
        'unload'     => false,
        // Flag that indicates if this is a WordPress.org plugin/theme
        'is_public'  => false,
    ],
    'addons' => [],
];
