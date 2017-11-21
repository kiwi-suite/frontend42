<?php
namespace Frontend42\View\Helper;

use Frontend42\Selector\PageListSelector;
use Zend\Form\View\Helper\AbstractHelper;

class PageList extends AbstractHelper
{
    /**
     * @var PageListSelector
     */
    protected $pageListSelector;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var int
     */
    protected $sitemapId;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var
     */
    protected $sortDirection = SORT_ASC;

    /**
     * @var string
     */
    protected $sort = PageListSelector::SORT_SITEMAP;

    /**
     * PageList constructor.
     * @param PageListSelector $pageListSelector
     */
    public function __construct(PageListSelector $pageListSelector)
    {
        $this->pageListSelector = $pageListSelector;
    }

    /**
     * @return PageListSelector
     */
    protected function getPageListSelector()
    {
        return clone $this->pageListSelector;
    }

    /**
     * @param $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

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
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $sortDirection
     * @return $this
     */
    public function enableSitemapSort($sortDirection = SORT_ASC)
    {
        $this->sort = PageListSelector::SORT_SITEMAP;
        if (!in_array($sortDirection, [SORT_ASC, SORT_DESC])) {
            $sortDirection = SORT_ASC;
        }
        $this->sortDirection = $sortDirection;

        return $this;
    }

    /**
     * @param int $sortDirection
     * @return $this
     */
    public function enableCreatedSort($sortDirection = SORT_DESC)
    {
        $this->sort = PageListSelector::SORT_CREATED;
        if (!in_array($sortDirection, [SORT_ASC, SORT_DESC])) {
            $sortDirection = SORT_DESC;
        }
        $this->sortDirection = $sortDirection;

        return $this;
    }

    /**
     * @param int $sortDirection
     * @return $this
     */
    public function enableNameSort($sortDirection = SORT_DESC)
    {
        $this->sort = PageListSelector::SORT_NAME;
        if (!in_array($sortDirection, [SORT_ASC, SORT_DESC])) {
            $sortDirection = SORT_DESC;
        }
        $this->sortDirection = $sortDirection;

        return $this;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        $selector = clone $this->getPageListSelector();

        $locale = $this->locale;
        if (empty($locale)) {
            $locale = $this->getView()->localization()->getActiveLocale();
        }

        $selector->setSitemapId($this->sitemapId)
            ->setLocale($locale)
            ->setLimit($this->limit)
            ->setSortDirection($this->sortDirection);

        if ($this->sort === PageListSelector::SORT_SITEMAP) {
            $selector->enableSitemapSort();
        } elseif ($this->sort === PageListSelector::SORT_CREATED) {
            $selector->enableCreatedSort();
        } elseif ($this->sort === PageListSelector::SORT_NAME) {
            $selector->enableNameSort();
        }

        return $selector->getResult();

    }

    /**
     * @param int $sitemapId
     * @param string $locale
     * @param int $limit
     * @return array|PageList
     */
    public function __invoke($sitemapId = null, $locale = null, $limit = null)
    {
        $getResult = false;

        if (!empty($sitemapId)) {
            $this->setSitemapId($sitemapId);
            $getResult = true;
        }

        if (!empty($locale)) {
            $this->setLocale($locale);
            $getResult = true;
        }

        if (!empty($limit)) {
            $this->setLimit($limit);
            $getResult = true;
        }

        if ($getResult === true) {
            return $this->getResult();
        }

        return $this;
    }
}
