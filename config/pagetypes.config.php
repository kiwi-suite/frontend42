<?php

return [
    'page_types' => [
        'paths' => [],

        'service_manager' => [
            'invokables' => [
                'Frontend42\Page' => 'Frontend42\PageType\Page',
            ],
        ],
    ]
];
