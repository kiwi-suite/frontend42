<?php
namespace Frontend42\Mvc\Controller\Plugin;

use Core42\Navigation\Container;
use Core42\Navigation\Navigation;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Page extends AbstractPlugin
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Navigation
     */
    private $navigation;

    /**
     * @param Container $container
     * @param Navigation $navigation
     */
    public function __construct(Container $container, Navigation $navigation)
    {
        $this->navigation = $navigation;

        $this->container = $container;
    }

    public function __invoke($pageId = null)
    {
        if ($pageId !== null) {
            return $this->getPage($pageId);
        }

        return $this;
    }

    public function getPage($pageId)
    {
        return $this->container->findOneByOption("pageId", $pageId);
    }

    public function getRoute($pageId)
    {
        $page = $this->getPage($pageId);

        if ($page == null) {
            return "";
        }

        return $page->getOption("route");
    }
}
