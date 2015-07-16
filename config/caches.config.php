<?php
return [
    'caches' => [
        'Cache\Sitemap' => [
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'cache_dir'      => 'data/cache/sitemap/',
                    'namespace'      => 'cache_sitemap',
                    'dirPermission'  => 0770,
                    'filePermission' => 0660,
                    'readable'       => true,
                    'writable'       => true,
                ],
            ],
            'plugins' => [
                'Serializer'
            ],
        ],
    ],
];
