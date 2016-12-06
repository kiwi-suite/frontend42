<?php
namespace Frontend42;

use Frontend42\Link\Adapter\SitemapLink;

return [
    'link' => [
        'adapter' => [
            'sitemap' => SitemapLink::class,
        ],
    ],
];
