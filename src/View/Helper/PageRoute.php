<?php
namespace Frontend42\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Frontend42\Page\PageRoute as PageRouteContainer;

class PageRoute extends AbstractHelper
{
    /**
     * @var PageRouteContainer
     */
    protected $pageRoute;

    protected $enableAssembleUrl = true;

    /**
     * Page constructor.
     * @param PageRouteContainer $pageRoute
     */
    public function __construct(PageRouteContainer $pageRoute)
    {
        $this->pageRoute = $pageRoute;
    }

    /**
     * @param bool $enableAssembleUrl
     * @return $this
     */
    public function __invoke($enableAssembleUrl = null)
    {
        if ($enableAssembleUrl !== null) {
            $this->enableAssembleUrl($enableAssembleUrl);
        }
        return $this;
    }

    /**
     * @param $enableAssembleUrl
     * @return $this
     */
    public function enableAssembleUrl($enableAssembleUrl)
    {
        $this->enableAssembleUrl = $enableAssembleUrl;

        return $this;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments = [])
    {
        return call_user_func_array([$this->pageRoute, $method], $arguments);
    }

    /**
     * @param int $pageId
     * @return string
     */
    public function fromPageId($pageId)
    {
        $route = $this->pageRoute->getRouteByPageId((int)$pageId);

        if ($this->enableAssembleUrl === false) {
            return $route;
        }
    }

    /**
     * @deprecated
     *
     * @param int $page
     * @return string
     */
    public function fromPage($page)
    {
        return $this->fromPageId($page);
    }

    /**
     * @param $handle
     * @param null $locale
     * @return bool|string
     */
    public function fromHandle($handle, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getView()->localization()->getActiveLocale();
        }

        $route = $this->pageRoute->getRouteByHandle($handle, $locale);

        if ($this->enableAssembleUrl === false) {
            return $route;
        }
    }

    public function fromSitemapId($sitemapId, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getView()->localization()->getActiveLocale();
        }

        $route = $this->pageRoute->getRouteBySitemapId($sitemapId, $locale);

        if ($this->enableAssembleUrl === false) {
            return $route;
        }
    }

    public function switchLanguage($pageId, $locale)
    {
        $route = $this->pageRoute->getSwitchLanguageRoute($pageId, $locale);

        if ($this->enableAssembleUrl === false) {
            return $route;
        }
    }
}
