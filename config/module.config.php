<?php
namespace Frontend42;

return array(
    'service_manager' => array(
        'factories' => array(
            'Frontend42\PageTypePluginManager' => 'Frontend42\PageType\Service\PageTypePluginManagerFactory',

            'Frontend42\SitemapProvider' => 'Frontend42\Sitemap\Service\SitemapProviderFactory',

            'Frontend42\LocaleOptions'  => 'Frontend42\I18n\Locale\Service\LocaleOptionsFactory',

            'Frontend42\Navigation\Provider' => 'Frontend42\Navigation\Provider\Service\DatabaseProviderFactory',
        ),

        'aliases' => array(
            'PageType' => 'Frontend42\PageTypePluginManager',
        ),
    ),

    'view_helpers' => array(
        'factories' => array(
            'page'    => 'Frontend42\View\Helper\Service\PageFactory',
            'locale'    => 'Frontend42\View\Helper\Service\LocaleFactory',
        ),
    ),

    'controller_plugins' => array(
        'factories' => array(
            'page'  => 'Frontend42\Mvc\Controller\Plugin\Service\PageFactory',
        ),
    ),

    'view_manager' => array(
        'template_path_stack'       => array(
            __NAMESPACE__               => __DIR__ . '/../view',
        ),
    ),

    'forms' => array(
        'factories' => array(
            'Frontend42\PageAdd' => 'Frontend42\Form\Service\PageAddFormFactory'
        ),
    ),

    'page_type_manager' => array(
        'factories' => array(
            'Frontend42\Content' => 'Frontend42\PageType\Service\ContentPageFactory'
        )
    ),

    'page_types' => array(
        'content' => array(
            'name' => 'Content',
            'class' => 'Frontend42\Content',
        ),
    ),

    'migration' => array(
        'directory'     => array(
            __NAMESPACE__ => __DIR__ . '/../data/migrations'
        ),
    ),
);
