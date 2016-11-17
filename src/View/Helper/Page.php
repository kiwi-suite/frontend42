<?php
namespace Frontend42\View\Helper;

use Core42\View\Helper\Proxy;
use Frontend42\Router\PageRoute;
use Frontend42\Selector\PageSelector;

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
     * Page constructor.
     * @param PageSelector $pageSelector
     * @param PageRoute $pageRoute
     */
    public function __construct(PageSelector $pageSelector, PageRoute $pageRoute)
    {
        $this->pageSelector = $pageSelector;
        $this->pageRoute = $pageRoute;
    }

    /**
     * @param null $pageId
     * @return $this
     */
    public function __invoke($pageId = null)
    {
        if ($pageId !== null) {
            $page = $this->pageSelector->setPageId((int)$pageId)->getResult();

            if ($page instanceof \Frontend42\Model\Page) {
                $this->object = $page;
            }
        }

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
    public function getUrl(array $params = [])
    {
        if ($this->getId()) {
            return $this->pageRoute->assemble($this->getId(), $params);
        }

        return "";
    }
}
