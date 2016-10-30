<?php
namespace Frontend42;

use Frontend42\PageType\LocalizationPageType;
use Frontend42\PageType\Service\LocalizationPageTypeFactory;

return [
    'page_types' => [

        'search_path' => [],
        'page_types' => [],
        'service_manager' => [
            'factories' => [
                LocalizationPageType::class     => LocalizationPageTypeFactory::class,
            ]
        ],
    ],
];
