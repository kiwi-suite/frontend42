<?php
namespace Frontend42;

return [
    'permissions' => [
        'service' => [
            'admin42' => [
                'role_provider' => [
                    'options' => [
                        'admin' => [
                            'permissions' => [
                                'sitemap/manage*'
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
