<?php
namespace Frontend42;

use Frontend42\Middleware\FrontendMiddleware;
use Zend\Router\Http\Method;

return [
    'router' => [
        'routes' => [
            'frontend' => [
                'type' => Method::class,
                'options' => [
                    'verb' => 'post,get',
                    'defaults' => [
                        'middleware' => FrontendMiddleware::class,
                    ]
                ],
                'may_terminate' => false,
                'child_routes' => [],
            ],
        ],
    ],
];
