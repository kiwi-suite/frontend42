<?php
namespace Frontend42;

use Frontend42\Command\XmlSitemap\FrontendCommand;

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
            'page'            => 'Frontend42\View\Helper\Service\PageFactory',
            'pageRoute'       => 'Frontend42\View\Helper\Service\PageRouteFactory',
            'block'           => 'Frontend42\View\Helper\Service\BlockFactory',
        ],
    ],

    'form_elements' => [
        'factories' => [
            'page_type_selector'        => 'Frontend42\FormElements\Service\PageTypeSelectorFactory',
            'page_selector'             => 'Frontend42\FormElements\Service\PageSelectorFactory',
            'block'                     => 'Frontend42\FormElements\Service\BlockFactory',
        ],
    ],

    'service_manager' => [
        'invokables' => [
            'Frontend42\PageTypeContent' => 'Frontend42\PageType\PageTypeContent',
        ],
        'factories' => [
            'Frontend42\PageTypeProvider'    => 'Frontend42\PageType\Service\PageTypeProviderFactory',
            'Frontend42\BlockProvider'       => 'Frontend42\Block\Service\BlockProviderFactory',
            'Frontend42\Navigation\Provider' => 'Frontend42\Navigation\Provider\Service\ProviderFactory',
            'Frontend42\Navigation\PageHandler' => 'Frontend42\Navigation\Service\PageHandlerFactory',

            'Frontend42\Link\SitemapLink' => 'Frontend42\Link\Adapter\Service\SitemapLinkFactory',

            'Frontend42\Sitemap\EventManager' => 'Frontend42\Event\Service\SitemapEventManagerFactory',
            'Frontend42\Block\EventManager' => 'Frontend42\Event\Service\BlockEventManagerFactory',
        ],
    ],

    'link' => [
        'adapter' => [
            'sitemap' => 'Frontend42\Link\SitemapLink',
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
