<?php
namespace Frontend42;

return [
    'view_manager' => array(
        'template_path_stack'       => array(
            __NAMESPACE__               => __DIR__ . '/../view',
        ),
    ),

    'migration' => [
        'directory'     => [
            __NAMESPACE__ => __DIR__ . '/../data/migrations'
        ],
    ],

    'form_elements' => [
        'factories' => [
            'page_type_selector'        => 'Frontend42\FormElements\Service\PageTypeSelectorFactory',
            'page_selector'             => 'Frontend42\FormElements\Service\PageSelectorFactory',
            'block'                     => 'Frontend42\FormElements\Service\BlockFactory',
        ],
    ],

    'service_manager' => [
        'invokables' => [
            'Frontend42\PageTypeContent' => 'Frontend42\PageType\PageTypeContent',
        ],
        'factories' => [
            'Frontend42\PageTypeProvider'    => 'Frontend42\PageType\Service\PageTypeProviderFactory',
            'Frontend42\BlockProvider'       => 'Frontend42\Block\Service\BlockProviderFactory',
        ],
    ],
];
