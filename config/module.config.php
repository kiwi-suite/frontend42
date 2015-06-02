<?php
namespace Frontend42;

return array(
    'view_manager' => array(
        'template_path_stack'       => array(
            __NAMESPACE__               => __DIR__ . '/../view',
        ),
    ),

    'migration' => array(
        'directory'     => array(
            __NAMESPACE__ => __DIR__ . '/../data/migrations'
        ),
    ),

    'form_elements' => array(
        'factories' => array(
            'page_type_selector'        => 'Frontend42\FormElements\Service\PageTypeSelectorFactory',
            'page_selector'             => 'Frontend42\FormElements\Service\PageSelectorFactory',
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'Frontend42\PageTypeProvider'    => 'Frontend42\PageType\Service\PageTypeProviderFactory',
        ),
    ),
);
