<?php
namespace Frontend42;

return [
    'permissions' => [
        'permission_service' => [
            'admin42' => [
                'admin' => [
                    'permissions' => [
                        'sitemap/manage*',
                        'sitemap/locale*',
                    ],
                ],

                'editor' => [
                    'inherit_from' => 'user',
                    'permissions' => [
                        'route/admin/sitemap*',
                        'dynamic/manage*',
                        'sitemap/manage*',
                        'sitemap/locale*',
                    ],
                    'options' => [
                        'redirect_after_login' => 'admin/sitemap',
                        'assignable'           => true,
                    ],
                ],
            ],
        ],
    ],
];
