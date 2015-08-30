<?php
return [
    'caches' => [
        'Cache\Sitemap' => [
            'adapter' => [
                'name' => 'memory',
            ],
            'plugins' => [
                'Serializer'
            ],
        ],
        'Cache\Block' => [
            'adapter' => [
                'name' => 'memory',
            ],
            'plugins' => [
                'Serializer'
            ],
        ],
    ],
];
