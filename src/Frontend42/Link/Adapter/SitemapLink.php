<?php
namespace Frontend42\Link\Adapter;

use Admin42\Link\Adapter\AdapterInterface;
use Frontend42\Navigation\PageHandler;
use Frontend42\TableGateway\PageTableGateway;
use Zend\Mvc\Router\RouteStackInterface;

class SitemapLink implements AdapterInterface
{
    /**
     * @var PageTableGateway
     */
    protected $pageTableGateway;

    /**
     * @var RouteStackInterface
     */
    protected $router;

    /**
     * @var PageHandler
     */
    protected $pageHandler;

    /**
     * SitemapLink constructor.
     * @param PageTableGateway $pageTableGateway
     * @param RouteStackInterface $router
     * @param PageHandler $pageHandler
     */
    public function __construct(
        PageTableGateway $pageTableGateway,
        RouteStackInterface $router,
        PageHandler $pageHandler
    ) {
        $this->pageTableGateway = $pageTableGateway;

        $this->router = $router;

        $this->pageHandler = $pageHandler;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function assemble($value)
    {
        if (empty($value["id"])) {
            return "";
        }

        $routeName = $this->pageHandler->getRouteByPage($value['id']);

        if (empty($routeName)) {
            return "";
        }

        return $this->router->assemble([], ['name' => $routeName]);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function getDisplayName($value)
    {
        if (empty($value["id"])) {
            return "";
        }

        $page = $this->pageTableGateway->selectByPrimary((int) $value['id']);
        if (empty($page)) {
            return "";
        }

        return $page->getName();
    }
}
