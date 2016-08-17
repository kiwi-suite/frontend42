<?php
namespace Frontend42;

use Frontend42\Navigation\Provider\Provider;

return array(
    'navigation' => array(
        'containers' => array(
            'frontend42' => Provider::class,

            'admin42' => array(
                'content' => [
                    'pages' => [
                        'sitemap' => array(
                            'options' => array(
                                'label' => 'label.sitemap',
                                'route' => 'admin/sitemap',
                                'icon' => 'fa fa-sitemap fa-fw',
                                'order' => 1000,
                                'permission' => 'route/admin/sitemap'
                            ),
                        ),
                    ]
                ]
            ),
        ),
    ),
);
