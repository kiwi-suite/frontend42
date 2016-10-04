<?php
namespace Frontend42;

use Frontend42\PageType\LocalizationPageType;
use Frontend42\PageType\Service\LocalizationPageTypeFactory;

return [
    'page_types' => [
        'paths' => [],

        'default_handle' => '',

        'service_manager' => [
            'factories' => [
                LocalizationPageType::class => LocalizationPageTypeFactory::class
            ],
        ],
    ]
];
