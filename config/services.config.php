<?php
namespace Frontend42;

use Frontend42\Event\PageEventListener;
use Frontend42\Event\Service\PageEventListenerFactory;
use Frontend42\Event\Service\PageEventManagerFactory;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\PageType\Service\PageTypePluginManagerFactory;

return [
    'service_manager' => [
        'factories' => [
            PageTypePluginManager::class        => PageTypePluginManagerFactory::class,
            PageEventListener::class            => PageEventListenerFactory::class,
            'Frontend42\Page\EventManager'      => PageEventManagerFactory::class,
        ],
    ],
];
