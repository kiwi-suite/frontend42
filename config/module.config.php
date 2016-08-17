<?php
namespace Frontend42;

use Cocur\Slugify\Slugify;
use Frontend42\Block\BlockProvider;
use Frontend42\Block\Service\BlockProviderFactory;
use Frontend42\Command\XmlSitemap\FrontendCommand;
use Frontend42\Event\PageEventListener;
use Frontend42\Event\Service\BlockEventManagerFactory;
use Frontend42\Event\Service\PageEventListenerFactory;
use Frontend42\Event\Service\PageEventManagerFactory;
use Frontend42\Event\Service\SitemapEventManagerFactory;
use Frontend42\Link\Adapter\Service\SitemapLinkFactory;
use Frontend42\Link\Adapter\SitemapLink;
use Frontend42\Mvc\Router\Service\HttpRouterFactory;
use Frontend42\Navigation\PageHandler;
use Frontend42\Navigation\Provider\Provider;
use Frontend42\Navigation\Provider\Service\ProviderFactory;
use Frontend42\Navigation\Service\PageHandlerFactory;
use Frontend42\PageType\Provider\PageTypeProvider;
use Frontend42\PageType\Provider\Service\PageTypeProviderFactory;
use Frontend42\View\Helper\Block;
use Frontend42\View\Helper\Page;
use Frontend42\View\Helper\PageRoute;
use Frontend42\View\Helper\Service\BlockFactory;
use Frontend42\View\Helper\Service\PageFactory;
use Frontend42\View\Helper\Service\PageRouteFactory;
use Zend\Router\Http\TreeRouteStack;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'view_manager' => [
        'template_path_stack'       => [
            __NAMESPACE__               => __DIR__ . '/../view',
        ],
    ],

    'migration' => [
        'directory'     => [
            __NAMESPACE__ => __DIR__ . '/../data/migrations'
        ],
    ],

    'view_helpers' => [
        'factories' => [
            Page::class            => PageFactory::class,
            PageRoute::class       => PageRouteFactory::class,
            Block::class           => BlockFactory::class,
        ],
        'aliases' => [
            'page'            => Page::class,
            'pageRoute'       => PageRoute::class,
            'block'           => Block::class,
        ]
    ],

    'service_manager' => [
        'factories' => [
            TreeRouteStack::class               => HttpRouterFactory::class,
            PageTypeProvider::class             => PageTypeProviderFactory::class,
            BlockProvider::class                => BlockProviderFactory::class,
            Provider::class                     => ProviderFactory::class,
            PageHandler::class                  => PageHandlerFactory::class,
            Slugify::class                      => InvokableFactory::class,

            SitemapLink::class                  => SitemapLinkFactory::class,

            PageEventListener::class            => PageEventListenerFactory::class,

            'Frontend42\Sitemap\EventManager'   => SitemapEventManagerFactory::class,
            'Frontend42\Block\EventManager'     => BlockEventManagerFactory::class,
            'Frontend42\Page\EventManager'      => PageEventManagerFactory::class,
        ],
    ],

    'link' => [
        'adapter' => [
            'sitemap' => SitemapLink::class,
        ],
    ],

    'xml_sitemap' => [
        'save_path' => 'public/sitemap',
        'page_type_mode' => 'whitelist', // whitelist, blackist 
        'page_types' => [],
        'commands' => [
            FrontendCommand::class
        ]
    ],
];
