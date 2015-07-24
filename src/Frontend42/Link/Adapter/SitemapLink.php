<?php
namespace Frontend42\Link\Adapter;

use Admin42\Link\Adapter\AdapterInterface;
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
     * @var array
     */
    protected $pageMapping;

    public function __construct(
        PageTableGateway $pageTableGateway,
        RouteStackInterface $router,
        $pageMapping
    ) {
        $this->pageTableGateway = $pageTableGateway;

        $this->router = $router;

        $this->pageMapping = $pageMapping;
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

        if (!array_key_exists($value["id"], $this->pageMapping)) {
            return "";
        }

        $name = $this->pageMapping[$value["id"]]['route'];

        return $this->router->assemble([], ['name' => $name]);
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