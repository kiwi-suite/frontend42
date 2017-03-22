<?php
namespace Frontend42;

use Frontend42\Controller\SitemapXmlController;
use Frontend42\Middleware\FrontendMiddleware;
use Zend\Router\Http\Method;
use Zend\Router\Http\Segment;

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
            'sitemap-xml' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/sitemap/[:filename].xml',
                    'defaults' => [
                        'controller' => SitemapXmlController::class,
                        'action' => 'xml'
                    ]
                ],
                'may_terminate' => true,
            ],
        ],
    ],
];
