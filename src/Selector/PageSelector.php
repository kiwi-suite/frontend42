<?php
namespace Frontend42\Selector;

use Core42\Selector\AbstractSelector;
use Core42\Selector\CacheAbleTrait;
use Frontend42\TableGateway\PageTableGateway;

class PageSelector extends AbstractSelector
{
    use CacheAbleTrait;

    /**
     * @var int
     */
    protected $pageId;

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
        return "page" . $this->pageId;
    }

    /**
     * @return mixed
     */
    protected function getUncachedResult()
    {
        return $this->getTableGateway(PageTableGateway::class)->selectByPrimary((int) $this->pageId);
    }
}
