<?php
return array(
    'navigation' => array(
        'containers' => array(
            'frontend42' => 'Frontend42\Navigation\Provider',

            'admin42' => array(
                'content' => array(
                    'options' => array(
                        'label' => 'label.content',
                        'icon' => 'fa fa-gears fa-fw',
                        'order' => 300,
                    ),
                    'pages' => array(
                        'tree' => array(
                            'options' => array(
                                'label' => 'label.content.tree',
                                'icon' => 'fa fa-gears fa-sitemap',
                                'route' => 'admin/tree',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
