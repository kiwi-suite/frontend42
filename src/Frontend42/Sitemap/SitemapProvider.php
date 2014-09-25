<?php
namespace Frontend42\Sitemap;

use Frontend42\Model\Page;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;

class SitemapProvider
{
    /**
     * @var SitemapTableGateway
     */
    protected $sitemapTableGateway;

    /**
     * @var PageTableGateway
     */
    protected $pageTableGateway;

    /**
     * @var array|null
     */
    protected $tree = null;

    /**
     * @var array
     */
    protected $treeLocale = array();

    /**
     * @param SitemapTableGateway $sitemapTableGateway
     * @param PageTableGateway $pageTableGateway
     */
    public function __construct(SitemapTableGateway $sitemapTableGateway, PageTableGateway $pageTableGateway)
    {
        $this->sitemapTableGateway = $sitemapTableGateway;

        $this->pageTableGateway = $pageTableGateway;
    }

    /**
     * @return array
     */
    public function getTree()
    {
        if ($this->tree === null) {
            $this->loadTree();
        }

        return $this->tree;
    }

    /**
     * @return array
     */
    protected function loadTree()
    {
        $result = $this->sitemapTableGateway->select();
        $flatTree = array();

        /** @var \Frontend42\Model\Sitemap $_sitemap */
        foreach ($result as $_sitemap) {
            $flatTree[$_sitemap->getId()] = array(
                'model' => $_sitemap,
                'children' => array()
            );
        }

        $tree = array();
        foreach ($flatTree as &$treeEntry) {
            if ($treeEntry['model']->getParentId() > 0) {
                $parent =& $flatTree[$treeEntry['model']->getParentId()];
                $parent['children'][] =& $treeEntry;

                continue;
            }

            $tree[] =& $treeEntry;
        }

        $this->tree = $tree;
    }

    /**
     * @param $locale
     * @return array
     */
    public function getTreeWithLocale($locale)
    {
        if (isset($this->treeLocale[$locale])) {
            return $this->treeLocale[$locale];
        }

        $result = $this->pageTableGateway->select(array(
            'locale' => $locale
        ));

        /** @var Page[] $pages */
        $pages = array();
        /** @var Page $_page */
        foreach ($result as $_page) {
            $pages[$_page->getSitemapId()] = $_page;
        }

        $tree = $this->getTree();

        $recursiveFunction = function(&$tree) use (&$recursiveFunction, $pages){
            foreach ($tree as &$_tree) {
                if (isset($pages[$_tree['model']->getId()])) {
                    $_tree['language'] = $pages[$_tree['model']->getId()];
                }

                if (!empty($_tree['children'])) {
                    $recursiveFunction($_tree['children']);
                }
            }
        };
        $recursiveFunction($tree);
        $this->treeLocale[$locale] = $tree;

        return $this->treeLocale[$locale];
    }
}
