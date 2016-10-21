<?php
namespace Frontend42;

use Frontend42\View\Helper\Form\FormBlock;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'view_manager' => [
        'template_path_stack'       => [
            __NAMESPACE__               => __DIR__ . '/../../view',
        ],
    ],
    'view_helpers' => [
        'factories'  => [
            FormBlock::class        => InvokableFactory::class,
        ],
        'aliases' => [
            'formBlock'             => FormBlock::class,
        ],
    ],
];
