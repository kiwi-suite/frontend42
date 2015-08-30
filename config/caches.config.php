<?php
return [
    'caches' => [
        'Cache\Sitemap' => [
            'adapter' => [
                'name' => 'memory',
                'options' => [
                    'namespace' => 'sitemap',
                ],
            ],
            'plugins' => [
                'Serializer'
            ],
        ],
        'Cache\Block' => [
            'adapter' => [
                'name' => 'memory',
                'options' => [
                    'namespace' => 'block',
                ],
            ],
            'plugins' => [
                'Serializer'
            ],
        ],
    ],
];
