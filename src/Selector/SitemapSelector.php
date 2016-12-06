<?php
namespace Frontend42\Selector;

use Core42\Selector\AbstractSelector;
use Core42\Selector\CacheAbleTrait;
use Frontend42\TableGateway\SitemapTableGateway;

class SitemapSelector extends AbstractSelector
{
    use CacheAbleTrait;

    /**
     * @var int
     */
    protected $sitemapId;

    /**
     * @param int $sitemapId
     * @return $this
     */
    public function setSitemapId($sitemapId)
    {
        $this->sitemapId = $sitemapId;

        return $this;
    }

    /**
     * @return string
     */
    protected function getCacheName()
    {
        return "sitemap";
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return "sitemap" . $this->sitemapId;
    }

    /**
     * @return mixed
     */
    protected function getUncachedResult()
    {
        return $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary((int) $this->sitemapId);
    }
}
