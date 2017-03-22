<?php
namespace Frontend42;

use Frontend42\Command\SitemapXml\GenerateCommand;

return [
    'cli' => [
        'create-sitemap-xml' => [
            'group'                     => '*',
            'route'                     => 'create-sitemap-xml',
            'command-name'              => GenerateCommand::class,
            'description'               => 'Generates a sitemap.xml',
            'short_description'         => 'Generates a sitemap.xml',
        ],
    ],
];
