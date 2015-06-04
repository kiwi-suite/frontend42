<?php
namespace Frontend42;

return [
    'router' => [
        'routes' => [
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
