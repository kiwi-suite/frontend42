<?php
namespace Frontend42;

use Frontend42\Selector\SitemapXmlSelector;

return [
    'sitemap-xml' => [
        'location' => 'data/xml-sitemap',
        'selector' => [
            SitemapXmlSelector::class
        ],
    ],
];
