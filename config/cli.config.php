<?php
namespace Frontend42;

use Frontend42\Command\Router\CreateRouteConfigCommand;
use Frontend42\Command\XmlSitemap\GenerateCommand;

return [
    'cli' => [
        'create-frontend-routes' => [
            'route'                     => 'create-frontend-routes',
            'command-name'              => CreateRouteConfigCommand::class,
            'description'               => '',
            'short_description'         => '',
            'options_descriptions'      => [
            ]
        ],

        'create-sitemap' => [
            'route'                     => 'create-sitemap',
            'command-name'              => GenerateCommand::class,
            'description'               => '',
            'short_description'         => '',
            'options_descriptions'      => [
            ]
        ],
    ],
];
