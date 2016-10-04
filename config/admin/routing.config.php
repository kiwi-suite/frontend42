<?php
namespace Frontend42;

use Core42\Mvc\Router\Http\AngularSegment;
use Frontend42\Controller\BlockController;
use Frontend42\Controller\SitemapController;
use Frontend42\Middleware\FrontendMiddleware;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Method;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'frontend' => [
                'type' => Method::class,
                'options' => [
                    'verb' => 'post,get,put,delete',
                    'defaults' => [
                        'middleware' => FrontendMiddleware::class,
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [],
            ],
            'admin' => [
                'child_routes' => [
                    'sitemap' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => 'sitemap/',
                            'defaults' => [
                                'controller' => SitemapController::class,
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'list' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => 'list/',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'save' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => 'save/',
                                    'defaults' => [
                                        'action' => 'save',
                                    ],
                                ],
                            ],
                            'add-sitemap' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => 'add-sitemap/',
                                    'defaults' => [
                                        'action' => 'add-sitemap',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => AngularSegment::class,
                                'options' => [
                                    'route' => 'edit/:id/[:version/]',
                                    'defaults' => [
                                        'action' => 'edit',
                                        'approve' => false,
                                    ],
                                ],
                            ],
                            'preview' => [
                                'type' => AngularSegment::class,
                                'options' => [
                                    'route' => 'preview/:id/[:version/]',
                                    'defaults' => [
                                        'action' => 'preview',
                                    ],
                                ],
                            ],
                            'edit-approve' => [
                                'type' => AngularSegment::class,
                                'options' => [
                                    'route' => 'edit/:id/[:version/]',
                                    'defaults' => [
                                        'action' => 'edit',
                                        'approve' => true,
                                    ],
                                ],
                            ],
                            'approve' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => 'approve/:id/:version',
                                    'defaults' => [
                                        'action' => 'approve',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => 'delete/',
                                    'defaults' => [
                                        'action' => 'delete',
                                    ],
                                ],
                            ],
                            'change-language' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => 'change-language/:locale/:sitemapId/',
                                    'defaults' => [
                                        'action' => 'change-language',
                                    ],
                                ],
                            ],
                            'change-page-type' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => 'change-page-type/:pageId/:sitemapId/',
                                    'defaults' => [
                                        'action' => 'change-page-type',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'block' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => 'block/',
                            'defaults' => [
                                'controller' => BlockController::class,
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'inheritance-save' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => 'inheritance-save/',
                                    'defaults' => [
                                        'action' => 'save-inheritance',
                                    ],
                                ],
                            ],
                            'inheritance-clean' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => 'inheritance-clean/',
                                    'defaults' => [
                                        'action' => 'clean-inheritance',
                                    ],
                                ],
                            ],
                            'inheritance-list' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => 'inheritance-list/',
                                    'defaults' => [
                                        'action' => 'list-inheritance-page',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
