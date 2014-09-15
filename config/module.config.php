<?php
namespace Frontend42;

return array(
    'service_manager' => array(
        'factories' => array(
            'Frontend42\Tree'           => 'Frontend42\Tree\Service\TreeFactory',
            'Frontend42\LocaleOptions'  => 'Frontend42\I18n\Locale\Service\LocaleOptionsFactory',
        ),
    ),
    'router' => array(
        'router_class' => 'Frontend42\Mvc\Router\Http\Database',
    ),
);
