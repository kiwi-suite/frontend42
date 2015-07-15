<?php
namespace Frontend42;

return array(
    'navigation' => array(
        'containers' => array(
            'frontend42' => 'Frontend42\Navigation\Provider',

            'admin42' => array(
                'sitemap' => array(
                    'options' => array(
                        'label' => 'label.sitemap',
                        'route' => 'admin/sitemap',
                        'icon' => 'fa fa-sitemap fa-fw',
                    ),
                ),
            ),
        ),
    ),
);
