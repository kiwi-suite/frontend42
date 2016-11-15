<?php
namespace Frontend42\View\Helper;

use Core42\View\Helper\Proxy;
use Frontend42\Selector\PageSelector;
use Frontend42\Selector\SitemapSelector;

class Sitemap extends Proxy
{
    /**
     * @var SitemapSelector
     */
    protected $sitemapSelector;

    /**
     * Page constructor.
     * @param SitemapSelector $sitemapSelector
     */
    public function __construct(SitemapSelector $sitemapSelector)
    {
        $this->sitemapSelector = $sitemapSelector;
    }

    public function __invoke($sitemapId = null)
    {
        if ($sitemapId !== null) {
            $sitemap = $this->sitemapSelector->setSitemapId((int)$sitemapId)->getResult();
            if ($sitemap instanceof \Frontend42\Model\Sitemap) {
                $this->object = $sitemap;
            }
        }

        return $this;
    }
}
