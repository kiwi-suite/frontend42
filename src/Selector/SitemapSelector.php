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
     * @var string
     */
    protected $handle;

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
     * @param $handle
     * @return $this
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;

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
        if (!empty($this->handle)) {
            return "handle" . $this->handle;
        }
        return "sitemap" . $this->sitemapId;
    }

    /**
     * @return mixed
     */
    protected function getUncachedResult()
    {
        if (!empty($this->handle)) {
            $sitemapResult = $this->getTableGateway(SitemapTableGateway::class)->select([
                'handle' => $this->handle
            ]);

            if ($sitemapResult->count() == 0) {
                return null;
            }

            return $sitemapResult->current();
        }
        return $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary((int) $this->sitemapId);
    }
}
