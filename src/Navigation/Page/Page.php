<?php
namespace Frontend42\Navigation\Page;

use Core42\Navigation\Page\AbstractPage;
use Frontend42\Page\Data\Data;
use Zend\Router\RouteStackInterface;

class Page extends AbstractPage
{
    /**
     * @var int
     */
    protected $sitemapId;

    /**
     * @var int
     */
    protected $pageId;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @var RouteStackInterface
     */
    protected $router;

    /**
     * @param Data $data
     * @param RouteStackInterface $router
     */
    public function __construct(Data $data, RouteStackInterface $router)
    {
        $this->data = $data;
        $this->router = $router;
    }

    /**
     * @return int
     */
    public function getSitemapId()
    {
        return $this->sitemapId;
    }

    /**
     * @param int $sitemapId
     */
    public function setSitemapId($sitemapId)
    {
        $this->sitemapId = $sitemapId;
    }

    /**
     * @return int
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @param int $pageId
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        $route = $this->data->getPageRoute($this->getPageId());
        if (empty($route)) {
            return "";
        }

        return $this->router->assemble([], ['name' => $route]);
    }
}
