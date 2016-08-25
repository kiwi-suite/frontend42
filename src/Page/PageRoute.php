<?php
namespace Frontend42\Page;

use Frontend42\Page\Data\Data;

class PageRoute
{
    /**
     * @var string
     */
    protected $defaultHandle;

    /**
     * @var Data
     */
    protected $data;

    /**
     * PageRoute constructor.
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * @param int $sitemapId
     * @param string $locale
     * @return bool|string
     */
    public function getRouteBySitemapId($sitemapId, $locale)
    {
        $pageId = $this->data->getLocaleMapping($sitemapId, $locale);

        if ($pageId === false) {
            $pageId = $this->getRouteByHandle($this->defaultHandle, $locale);
        }

        if ($pageId === false) {
            return false;
        }

        return $this->getRouteByPageId($pageId);
    }

    /**
     * @param string $handle
     * @param string $locale
     * @return bool|string
     */
    public function getRouteByHandle($handle, $locale)
    {
        $pageId = $this->data->getHandleMapping($handle, $locale);

        if (empty($pageId)) {
            return false;
        }

        return $this->getRouteByPageId($pageId);
    }

    /**
     * @param int $pageId
     * @return bool|string
     */
    public function getRouteByPageId($pageId)
    {
        return $this->data->getPageRoute($pageId);
    }

    /**
     * @param int $pageId
     * @param string $locale
     * @return bool|string
     */
    public function getSwitchLanguageRoute($pageId, $locale)
    {
        $page = $this->data->getPage($pageId);
        if (empty($page)) {
            return;
        }
        $sitemapId = $page->getId();

        if ($sitemapId === false) {
            $route = $this->getRouteByHandle($this->defaultHandle, $locale);
        } else {
            $route = $this->getRouteBySitemapId($sitemapId, $locale);
        }

        return $route;
    }
}
