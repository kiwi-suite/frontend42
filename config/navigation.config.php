<?php
namespace Frontend42;

use Frontend42\Navigation\Service\FrontendNavigationAbstractFactory;

return [
    'navigation' => [
        'nav' => [
            'main' => [
                'label' => 'label.nav.main'
            ],
        ],
        'service_manager' => [
            'abstract_factories' => [
                FrontendNavigationAbstractFactory::class,
            ],
        ],
    ],
];
