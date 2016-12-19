<?php
namespace Frontend42\Selector;

use Core42\Selector\AbstractSelector;
use Core42\Selector\CacheAbleTrait;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\View\Helper\Page;

class PageSelector extends AbstractSelector
{
    use CacheAbleTrait;

    /**
     * @var int
     */
    protected $pageId;

    /**
     * @var int
     */
    protected $sitemapId;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param int $pageId
     * @return $this
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

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
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return string
     */
    protected function getCacheName()
    {
        return "page";
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        if (!empty($this->locale) && !empty($this->sitemapId)) {
            return "psl" . $this->sitemapId . $this->locale;
        }
        return "page" . $this->pageId;
    }

    /**
     * @return mixed
     */
    protected function getUncachedResult()
    {
        if (!empty($this->locale) && !empty($this->sitemapId)) {
            $pageResult = $this->getTableGateway(PageTableGateway::class)->select([
                'sitemapId' => (int) $this->sitemapId,
                'locale' => (string) $this->locale
            ]);

            if ($pageResult->count() == 0) {
                return null;
            }

            return $pageResult->current();
        }

        return $this->getTableGateway(PageTableGateway::class)->selectByPrimary((int) $this->pageId);
    }
}
