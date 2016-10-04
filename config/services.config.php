<?php
namespace Frontend42;

use Cocur\Slugify\Slugify;
use Frontend42\Block\BlockProvider;
use Frontend42\Block\Service\BlockProviderFactory;
use Frontend42\Event\PageEventListener;
use Frontend42\Event\Service\PageEventListenerFactory;
use Frontend42\Event\Service\PageEventManagerFactory;
use Frontend42\Link\Adapter\Service\SitemapLinkFactory;
use Frontend42\Link\Adapter\SitemapLink;
use Frontend42\Middleware\FrontendMiddleware;
use Frontend42\Middleware\Service\FrontendMiddlewareFactory;
use Frontend42\Mvc\Router\Service\HttpRouterFactory;
use Frontend42\Navigation\Provider\Provider;
use Frontend42\Navigation\Provider\Service\ProviderFactory;
use Frontend42\Page\Data\Data;
use Frontend42\Page\Page;
use Frontend42\Page\PageRoute;
use Frontend42\Page\Service\DataFactory;
use Frontend42\Page\Service\MemoryDataFactory;
use Frontend42\Page\Service\PageFactory;
use Frontend42\Page\Service\PageRouteFactory;
use Frontend42\PageType\Provider\PageTypeConfigProvider;
use Frontend42\PageType\Provider\PageTypeProvider;
use Frontend42\PageType\Provider\Service\PageTypeConfigProviderFactory;
use Frontend42\PageType\Provider\Service\PageTypeProviderFactory;
use Zend\Router\Http\TreeRouteStack;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'factories' => [
            TreeRouteStack::class               => HttpRouterFactory::class,
            PageTypeProvider::class             => PageTypeProviderFactory::class,
            BlockProvider::class                => BlockProviderFactory::class,
            Data::class                         => DataFactory::class,
            FrontendMiddleware::class           => FrontendMiddlewareFactory::class,
            Page::class                         => PageFactory::class,
            PageRoute::class                    => PageRouteFactory::class,
            PageTypeConfigProvider::class       => PageTypeConfigProviderFactory::class,

            SitemapLink::class                  => SitemapLinkFactory::class,

            PageEventListener::class            => PageEventListenerFactory::class,

            'Frontend42\Page\MemoryData'        => MemoryDataFactory::class,

            'Frontend42\Page\EventManager'      => PageEventManagerFactory::class,
        ],
    ],
];
