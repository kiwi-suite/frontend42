<?php
namespace Frontend42\Link\Adapter;

use Admin42\Link\Adapter\AdapterInterface;
use Frontend42\Router\PageRoute;
use Frontend42\Selector\PageSelector;

class SitemapLink implements AdapterInterface
{
    /**
     * @var PageRoute
     */
    private $pageRoute;
    /**
     * @var PageSelector
     */
    private $pageSelector;

    /**
     * SitemapLink constructor.
     * @param PageRoute $pageRoute
     * @param PageSelector $pageSelector
     */
    public function __construct(PageRoute $pageRoute, PageSelector $pageSelector)
    {
        $this->pageRoute = $pageRoute;
        $this->pageSelector = $pageSelector;
    }

    /**
     * @param mixed $value
     * @param array $options
     * @return string
     */
    public function assemble($value, $options = [])
    {
        if (empty($value['id'])) {
            return "";
        }

        return $this->pageRoute->assemble($value['id']);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function getDisplayName($value)
    {
        if (empty($value['id'])) {
            return "";
        }

        $page = $this->pageSelector->setPageId($value['id'])->getResult();
        if (empty($page)) {
            return "";
        }

        return $page->getName();
    }

    /**
     * @return array
     */
    public function getPartials()
    {
        return [
            'link/sitemap.html' => 'link/sitemap',
            'link/sitemap/node.html' => 'link/sitemap-node',
        ];
    }
}
