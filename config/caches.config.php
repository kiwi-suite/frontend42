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
            'pageContent' => [
                'driver' => (DEVELOPMENT_MODE === true) ? 'development' : 'production',
                'namespace' =>  'pageContent',
            ],
            'routing' => [
                'driver' => (DEVELOPMENT_MODE === true) ? 'development' : 'production',
                'namespace' =>  'routing',
            ],
            'navigation' => [
                'driver' => (DEVELOPMENT_MODE === true) ? 'development' : 'production',
                'namespace' =>  'navigation',
            ],
        ],
    ],
];
