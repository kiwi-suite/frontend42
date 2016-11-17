<?php
namespace Frontend42\Router;

use Frontend42\Model\Page;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\Selector\PageSelector;
use Frontend42\Selector\SitemapSelector;
use Zend\Router\RouteStackInterface;

class PageRoute
{
    /**
     * @var PageTypePluginManager
     */
    protected $pageTypePluginManager;

    /**
     * @var SitemapSelector
     */
    protected $sitemapSelector;

    /**
     * @var PageSelector
     */
    protected $pageSelector;

    /**
     * @var RouteStackInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * PageRoute constructor.
     * @param PageTypePluginManager $pageTypePluginManager
     * @param SitemapSelector $sitemapSelector
     * @param PageSelector $pageSelector
     * @param RouteStackInterface $router
     * @param $routePrefix
     */
    public function __construct(
        PageTypePluginManager $pageTypePluginManager,
        SitemapSelector $sitemapSelector,
        PageSelector $pageSelector,
        RouteStackInterface $router,
        $routePrefix
    ) {
        $this->pageTypePluginManager = $pageTypePluginManager;
        $this->sitemapSelector = $sitemapSelector;
        $this->pageSelector = $pageSelector;
        $this->router = $router;
        $this->routePrefix = $routePrefix;
    }

    /**
     * @param int $pageId
     * @param array $params
     * @return string
     */
    public function assemble($pageId, array $params = [])
    {
        $route = $this->getRoute($pageId);
        if (empty($route)) {
            return "";
        }

        return $this->router->assemble($params, ['name' => $route]);
    }

    /**
     * @param int $pageId
     * @return string
     */
    public function getRoute($pageId)
    {
        /** @var Page $page */
        $page = $this->pageSelector->setPageId((int) $pageId)->getResult();
        if (empty($page)) {
            return "";
        }

        return $this->routePrefix . '/' . $page->getRoute();
    }
}
