<?php
namespace Frontend42;

return [
    'cache' => [
        'caches' => [
            'page' => [
                'driver' => (DEVELOPMENT_MODE === true) ? 'development' : 'production',
                'namespace' =>  'page',
            ],
            'sitemap' => [
                'driver' => (DEVELOPMENT_MODE === true) ? 'development' : 'production',
                'namespace' =>  'sitemap',
            ],
            'page_content' => [
                'driver' => (DEVELOPMENT_MODE === true) ? 'development' : 'production',
                'namespace' =>  'page_content',
            ],
            'routing' => [
                'driver' => (DEVELOPMENT_MODE === true) ? 'development' : 'production',
                'namespace' =>  'routing',
            ],
        ],
    ],
];
