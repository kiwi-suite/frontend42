<?php
namespace Frontend42;

use Frontend42\Navigation\Service\ContainerFactory;

return [
    'navigation' => [
        'containers' => [
            'admin42' => [
                'content' => [
                    'pages' => [
                        'sitemap' => [
                            'label' => 'label.sitemap',
                            'route' => 'admin/sitemap',
                            'icon' => 'fa fa-sitemap fa-fw',
                            'order' => 1000,
                        ],
                    ]
                ]
            ],
        ],

        'service_manager' => [
            'factories' => [
                'frontend42' => ContainerFactory::class
            ],
        ],
    ],
];
