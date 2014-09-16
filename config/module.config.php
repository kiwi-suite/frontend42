<?php
namespace Frontend42;

return array(
    'service_manager' => array(
        'factories' => array(
            'Frontend42\Tree'           => 'Frontend42\Tree\Service\TreeFactory',
            'Frontend42\LocaleOptions'  => 'Frontend42\I18n\Locale\Service\LocaleOptionsFactory',

            'Frontend42\Navigation\Provider' => 'Frontend42\Navigation\Provider\Service\DatabaseProviderFactory'
        ),
    ),
    'router' => array(
        'router_class' => 'Frontend42\Mvc\Router\Http\Database',
    ),

    'view_helpers' => array(
        'factories' => array(
            'page'    => 'Frontend42\View\Helper\Service\PageFactory',
        ),
    ),
);
