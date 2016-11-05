<?php
namespace Frontend42;

use Core42\Mvc\Router\Http\AngularSegment;
use Frontend42\Controller\PageController;
use Frontend42\Controller\SitemapController;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
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
                            'sort-save' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => 'sort-save/',
                                    'defaults' => [
                                        'action' => 'sort-save',
                                    ],
                                ],
                            ],
                            'add-page' => [
                                'type' => AngularSegment::class,
                                'options' => [
                                    'route' => 'add-page/:locale/[:parentId/]',
                                    'defaults' => [
                                        'action' => 'add-page',
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
                        ],
                    ],
                    'page' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => 'page/',
                            'defaults' => [
                                'controller' => PageController::class,
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'edit' => [
                                'type' => AngularSegment::class,
                                'options' => [
                                    'route' => 'edit/:id/[:versionId/]',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'approve' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => 'approve/:versionId/',
                                    'defaults' => [
                                        'action' => 'approve',
                                    ],
                                ],
                            ],
                            'delete-version' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => 'delete-version/:id/:versionId/',
                                    'defaults' => [
                                        'action' => 'delete-version',
                                    ],
                                ],
                            ],
                            'change-locale' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => 'change-locale/:sitemapId/:locale/',
                                    'defaults' => [
                                        'action' => 'change-locale',
                                    ],
                                ],
                            ],
                        ],
                    ]
                ],
            ],
        ],
    ],
];
