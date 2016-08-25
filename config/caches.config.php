<?php
namespace Frontend42;
return [

    'cache' => [
        'caches' => [
            'frontend' => [
                'driver' => 'ephemeral',
                'namespace' => 'frontend',
            ],
            'sitemap' => [
                'driver' => 'ephemeral',
                'namespace' => 'sitemap',
            ],
            'page' => [
                'driver' => 'ephemeral',
                'namespace' => 'page',
            ],
            'pageversion' => [
                'driver' => 'ephemeral',
                'namespace' => 'pageversion',
            ],
            'block' => [
                'driver' => 'ephemeral',
                'namespace' => 'block',
            ],
        ],
    ],
];
