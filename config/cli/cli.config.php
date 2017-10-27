<?php
namespace Frontend42;

use Frontend42\Command\Page\AddMissingPagesCommand;
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

        'add-missing-pages' => [
            'group'                     => '*',
            'route'                     => 'add-missing-pages --userId=',
            'command-name'              => AddMissingPagesCommand::class,
            'description'               => 'Add missing pages',
            'short_description'         => 'Add missing pages',
        ],
    ],
];
