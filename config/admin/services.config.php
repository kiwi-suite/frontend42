<?php
namespace Frontend42;

use Frontend42\PageType\Service\PageForm;
use Frontend42\PageType\Service\PageFormFactory;

return [
    'service_manager' => [
        'factories' => [
            PageForm::class     => PageFormFactory::class
        ],
    ],
];
