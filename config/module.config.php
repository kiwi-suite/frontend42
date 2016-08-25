<?php
namespace Frontend42;

use Frontend42\Link\Adapter\SitemapLink;
use Frontend42\View\Helper\Block;
use Frontend42\View\Helper\Page;
use Frontend42\View\Helper\PageRoute;
use Frontend42\View\Helper\Service\BlockFactory;
use Frontend42\View\Helper\Service\PageFactory;
use Frontend42\View\Helper\Service\PageRouteFactory;

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
            Block::class            => BlockFactory::class,
            Page::class             => PageFactory::class,
            PageRoute::class        => PageRouteFactory::class,
        ],
        'aliases' => [
            'block'          => Block::class,
            'page'           => Page::class,
            'pageRoute'      => PageRoute::class,
        ]
    ],

    'controller_plugins' => [
        'factories' => [
            'page'    => \Frontend42\Mvc\Controller\Plugins\Service\PageFactory::class,
        ],
    ],

    'link' => [
        'adapter' => [
            'sitemap' => SitemapLink::class,
        ],
    ],
];
