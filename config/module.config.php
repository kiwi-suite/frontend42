<?php
namespace Frontend42;

return array(
    'service_manager' => array(
        'factories' => array(
            'Frontend42\Tree'           => 'Frontend42\Tree\Service\TreeFactory',

            'Frontend42\LocaleOptions'  => 'Frontend42\I18n\Locale\Service\LocaleOptionsFactory',

            'Frontend42\Navigation\Provider' => 'Frontend42\Navigation\Provider\Service\DatabaseProviderFactory',

            'Frontend42\PageType\Content' => 'Frontend42\Page\Service\ContentPageFactory',
            'Frontend42\PageType\Locked' => 'Frontend42\Page\Service\LockedPageFactory',
            'Frontend42\PageType\Start' => 'Frontend42\Page\Service\StartPageFactory',
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

    'page_types' => array(
        'content' => array(
            'name' => 'Content',
            'class' => 'Frontend42\PageType\Content',
        ),
    ),
);
