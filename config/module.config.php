<?php
namespace Frontend42;

use Frontend42\Command\XmlSitemap\FrontendCommand;
use Frontend42\Link\Adapter\Service\SitemapLinkFactory;
use Frontend42\Link\Adapter\SitemapLink;
use Frontend42\Mvc\Router\Service\HttpRouterFactory;
use Frontend42\View\Helper\Block;
use Frontend42\View\Helper\Page;
use Frontend42\View\Helper\PageRoute;
use Frontend42\View\Helper\Service\BlockFactory;
use Frontend42\View\Helper\Service\PageFactory;
use Frontend42\View\Helper\Service\PageRouteFactory;
use Zend\Router\Http\TreeRouteStack;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'view_manager' => array(
        'template_path_stack'       => array(
            __NAMESPACE__               => __DIR__ . '/../view',
        ),
    ),

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
            TreeRouteStack::class       => HttpRouterFactory::class,
            'Frontend42\PageTypeContent' => InvokableFactory::class,
            'Frontend42\PageTypeProvider'    => 'Frontend42\PageType\Service\PageTypeProviderFactory',
            'Frontend42\BlockProvider'       => 'Frontend42\Block\Service\BlockProviderFactory',
            'Frontend42\Navigation\Provider' => 'Frontend42\Navigation\Provider\Service\ProviderFactory',
            'Frontend42\Navigation\PageHandler' => 'Frontend42\Navigation\Service\PageHandlerFactory',

            SitemapLink::class => SitemapLinkFactory::class,

            'Frontend42\Sitemap\EventManager' => 'Frontend42\Event\Service\SitemapEventManagerFactory',
            'Frontend42\Block\EventManager' => 'Frontend42\Event\Service\BlockEventManagerFactory',
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
