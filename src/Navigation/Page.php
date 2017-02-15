<?php
namespace Frontend42\Navigation;

use Core42\Navigation\Page\AbstractPage;
use Core42\Navigation\Page\PageInterface;
use Frontend42\Router\PageRoute;
use Frontend42\Selector\PageSelector;
use Zend\Router\Http\RouteMatch;

class Page extends AbstractPage implements PageInterface
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var PageSelector
     */
    protected $pageSelector;

    /**
     * @var PageRoute
     */
    protected $pageRoute;

    /**
     * @var int
     */
    protected $pageId;

    /**
     * Page constructor.
     * @param PageSelector $pageSelector
     * @param RouteMatch $routeMatch
     * @param PageRoute $pageRoute
     */
    public function __construct(
        PageSelector $pageSelector,
        PageRoute $pageRoute,
        RouteMatch $routeMatch = null
    ) {
        $this->pageSelector = $pageSelector;
        $this->routeMatch = $routeMatch;
        $this->pageRoute = $pageRoute;
    }

    /**
     * @param int $pageId
     * @return int
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this->pageId;
    }

    /**
     * @return int
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->pageRoute->assemble($this->pageId);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        if ($this->label === null) {
            $this->label = $this->pageSelector->setPageId($this->getPageId())->getResult()->getName();
        }

        return parent::getLabel();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        if (empty($this->routeMatch)) {
            return false;
        }
        
        $route = $this->pageRoute->getRoute($this->pageId);

        if (strlen($route) > strlen($this->routeMatch->getMatchedRouteName())) {
            return false;
        }

        if (substr($this->routeMatch->getMatchedRouteName(), 0, strlen($route)) != $route) {
            return false;
        }

        return true;
    }
}
