<?php
namespace Frontend42\View\Helper;

use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\Router\PageRoute;
use Frontend42\Selector\PageSelector;
use Frontend42\Selector\SitemapSelector;
use Zend\Form\View\Helper\AbstractHelper;

class SwitchLanguage extends AbstractHelper
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $fallbackHandle;

    /**
     * @var int
     */
    protected $sitemapId;

    /**
     * @var PageSelector
     */
    private $pageSelector;

    /**
     * @var PageRoute
     */
    private $pageRoute;

    /**
     * @var SitemapSelector
     */
    private $sitemapSelector;

    /**
     * SwitchLanguage constructor.
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
     * @param $locale
     * @param $fallbackHandle
     * @param int $sitemapId
     * @return $this
     */
    public function __invoke($locale, $fallbackHandle, $sitemapId = null)
    {
        $this->locale = $locale;
        $this->fallbackHandle = $fallbackHandle;
        if (empty($sitemapId)) {
            $sitemapId = $locale = $this->getView()->currentSitemap()->getId();
        }

        $this->sitemapId = $sitemapId;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        $id = $this->getPageId();
        if (empty($id)) {
            return "";
        }

        return $this->pageRoute->getRoute($id);
    }

    /**
     * @param array $params
     * @param array $options
     * @return string
     */
    public function getUrl(array $params = [], array $options = [])
    {
        $id = $this->getPageId();
        if (empty($id)) {
            return "";
        }

        return $this->pageRoute->assemble($id, $params, $options);
    }

    /**
     * @return int
     */
    protected function getPageId()
    {
        $sitemap = $this->sitemapSelector->setSitemapId($this->sitemapId)->getResult();
        if (!($sitemap instanceof Sitemap)) {
            return $this->getHandlePageId();
        }

        $page = $this->pageSelector->setLocale($this->locale)->setSitemapId($sitemap->getId())->getResult();
        if (!($page instanceof Page)) {
            return $this->getHandlePageId();
        }

        if ($page->getStatus() !== Page::STATUS_ONLINE) {
            return $this->getHandlePageId();
        }

        if ($page->getPublishedFrom() instanceof \DateTime && $page->getPublishedFrom()->getTimestamp() > time()) {
            return $this->getHandlePageId();
        }

        if ($page->getPublishedUntil() instanceof \DateTime && $page->getPublishedUntil()->getTimestamp() < time()) {
            return $this->getHandlePageId();
        }

        return $page->getId();
    }

    protected function getHandlePageId()
    {
        $sitemap = $this->sitemapSelector->setHandle($this->fallbackHandle)->getResult();
        if (!($sitemap instanceof Sitemap)) {
            return 0;
        }

        $page = $this->pageSelector->setLocale($this->locale)->setSitemapId($sitemap->getId())->getResult();
        if (!($page instanceof Page)) {
            return 0;
        }

        if ($page->getStatus() !== Page::STATUS_ONLINE) {
            return 0;
        }

        if ($page->getPublishedFrom() instanceof \DateTime && $page->getPublishedFrom()->getTimestamp() > time()) {
            return 0;
        }

        if ($page->getPublishedUntil() instanceof \DateTime && $page->getPublishedUntil()->getTimestamp() < time()) {
            return 0;
        }

        return $page->getId();
    }
}
