<?php
namespace Frontend42\View\Helper;

use Core42\View\Helper\Proxy;
use Frontend42\Model\Sitemap as SitemapModel;
use Frontend42\Router\PageRoute;
use Frontend42\Selector\PageSelector;
use Frontend42\Selector\SitemapSelector;

class Page extends Proxy
{
    /**
     * @var PageSelector
     */
    protected $pageSelector;

    /**
     * @var PageRoute
     */
    private $pageRoute;
    /**
     * @var SitemapSelector
     */
    private $sitemapSelector;

    /**
     * Page constructor.
     * @param PageSelector $pageSelector
     * @param PageRoute $pageRoute
     * @param SitemapSelector $sitemapSelector
     */
    public function __construct(
        PageSelector $pageSelector,
        PageRoute $pageRoute,
        SitemapSelector $sitemapSelector
    ) {
        $this->pageSelector = $pageSelector;
        $this->pageRoute = $pageRoute;
        $this->sitemapSelector = $sitemapSelector;
    }

    /**
     * @return PageSelector
     */
    protected function getPageSelector()
    {
        return clone $this->pageSelector;
    }

    /**
     * @return SitemapSelector
     */
    protected function getSitemapSelector()
    {
        return clone $this->sitemapSelector;
    }

    /**
     * @param null $pageId
     * @return $this
     */
    public function __invoke($pageId = null)
    {
        if ($pageId !== null) {
            $page = $this->getPageSelector()->setPageId((int)$pageId)->getResult();

            if ($page instanceof \Frontend42\Model\Page) {
                $this->object = $page;
            }
        }

        return $this;
    }

    /**
     * @param string $handle
     * @param string $locale
     * @return $this
     */
    public function loadByHandle($handle, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getView()->localization()->getActiveLocale();
        }

        $sitemap = $this->getSitemapSelector()->setHandle($handle)->getResult();
        if (!($sitemap instanceof SitemapModel)) {
            return $this;
        }

        $page = $this->getPageSelector()->setLocale($locale)->setSitemapId($sitemap->getId())->getResult();
        if (!($page instanceof \Frontend42\Model\Page)) {
            return $this;
        }

        $this->object = $page;

        return $this;
    }

    /**
     * @param int $sitemapId
     * @param string $locale
     * @return $this
     */
    public function loadBySitemapId($sitemapId, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getView()->localization()->getActiveLocale();
        }

        $sitemap = $this->getSitemapSelector()->setSitemapId($sitemapId)->getResult();
        if (!($sitemap instanceof SitemapModel)) {
            return $this;
        }

        $page = $this->getPageSelector()->setLocale($locale)->setSitemapId($sitemap->getId())->getResult();
        if (!($page instanceof \Frontend42\Model\Page)) {
            return $this;
        }

        $this->object = $page;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        if ($this->getId()) {
            return $this->pageRoute->getRoute($this->getId());
        }

        return "";
    }

    /**
     * @param array $params
     * @return string
     */
    public function getUrl(array $params = [], array $options = [])
    {
        if ($this->getId()) {
            return $this->pageRoute->assemble($this->getId(), $params, $options);
        }

        return "";
    }
}
