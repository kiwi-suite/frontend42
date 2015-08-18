<?php
namespace Frontend42;

return [
    'router' => [
        'router_class' => 'Frontend42\Mvc\Router\FrontendRouter',
        'routes' => [
            'frontend' => [
                'type' => 'method',
                'options' => [
                    'verb' => 'post,get,put,delete',
                ],
                'may_terminate' => false,
                'child_routes' => [],
            ],
            'admin' => [
                'child_routes' => [
                    'sitemap' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route' => 'sitemap/',
                            'defaults' => [
                                'controller' => __NAMESPACE__ . '\Sitemap',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'list' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route' => 'list/',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'save' => [
                                'type' => 'Zend\Mvc\Router\Http\Literal',
                                'options' => [
                                    'route' => 'save/',
                                    'defaults' => [
                                        'action' => 'save',
                                    ],
                                ],
                            ],
                            'add-sitemap' => [
                                'type' => 'Zend\Mvc\Router\Http\Literal',
                                'options' => [
                                    'route' => 'add-sitemap/',
                                    'defaults' => [
                                        'action' => 'add-sitemap',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => 'Core42\Mvc\Router\Http\AngularSegment',
                                'options' => [
                                    'route' => 'edit/:id/[:version/]',
                                    'defaults' => [
                                        'action' => 'edit',
                                        'approve' => false,
                                    ],
                                ],
                            ],
                            'preview' => [
                                'type' => 'Core42\Mvc\Router\Http\AngularSegment',
                                'options' => [
                                    'route' => 'preview/:id/[:version/]',
                                    'defaults' => [
                                        'action' => 'preview',
                                    ],
                                ],
                            ],
                            'edit-approve' => [
                                'type' => 'Core42\Mvc\Router\Http\AngularSegment',
                                'options' => [
                                    'route' => 'edit/:id/[:version/]',
                                    'defaults' => [
                                        'action' => 'edit',
                                        'approve' => true,
                                    ],
                                ],
                            ],
                            'approve' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route' => 'approve/:id/:version',
                                    'defaults' => [
                                        'action' => 'approve',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type' => 'Zend\Mvc\Router\Http\Literal',
                                'options' => [
                                    'route' => 'delete/',
                                    'defaults' => [
                                        'action' => 'delete',
                                    ],
                                ],
                            ],
                            'change-language' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route' => 'change-language/:locale/:sitemapId/',
                                    'defaults' => [
                                        'action' => 'change-language',
                                    ],
                                ],
                            ],
                            'change-page-type' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
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
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route' => 'block/',
                            'defaults' => [
                                'controller' => __NAMESPACE__ . '\Block',
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'inheritance-save' => [
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route' => 'inheritance-save/',
                                    'defaults' => [
                                        'action' => 'save-inheritance',
                                    ],
                                ],
                            ],
                            'inheritance-clean' => [
                                'type' => 'Zend\Mvc\Router\Http\Literal',
                                'options' => [
                                    'route' => 'inheritance-clean/',
                                    'defaults' => [
                                        'action' => 'clean-inheritance',
                                    ],
                                ],
                            ],
                            'inheritance-list' => [
                                'type' => 'Zend\Mvc\Router\Http\Literal',
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
