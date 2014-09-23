<?php
namespace Frontend42;

return array(
    'router' => array(
        'router_class' => 'Frontend42\Mvc\Router\Http\Database',
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'tree' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => 'tree/',
                            'defaults' => array(
                                'controller' => __NAMESPACE__ . '\Tree',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'edit' => array(
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route' => 'edit/:locale/:id/',
                                    'defaults' => array(
                                        'controller' => __NAMESPACE__ . '\Tree',
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'add' => array(
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route' => 'add/:locale/',
                                    'defaults' => array(
                                        'controller' => __NAMESPACE__ . '\Tree',
                                        'action' => 'add',
                                    ),
                                ),
                            ),
                            'add-element' => array(
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route' => 'add-element/',
                                    'defaults' => array(
                                        'controller' => __NAMESPACE__ . '\Tree',
                                        'action' => 'add-element',
                                    ),
                                ),
                            ),
                            'json' => array(
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route' => 'json/:locale/',
                                    'defaults' => array(
                                        'controller' => __NAMESPACE__ . '\Tree',
                                        'action' => 'json',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
