<?php
namespace Frontend42\Page;

use Frontend42\Model\Sitemap;
use Frontend42\Page\Data\Data;
use Frontend42\PageType\PageContent\PageContent;
use Frontend42\Selector\PageVersionSelector;

class Page
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * @var PageContent
     */
    protected $pageContent;

    /**
     * @var \Frontend42\Model\Page
     */
    protected $page;

    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * Page constructor.
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * @param $pageId
     * @param $versionName
     */
    public function initialize($pageId, $versionName)
    {
        $this->page = $this->data->getPage($pageId);
        $this->sitemap = $this->data->getSitemap($this->page->getSitemapId());
        $this->pageContent = $this->data->getPageContent($versionName, $this->page->getId());
    }

    /**
     * @return \Frontend42\Model\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return \Frontend42\Model\Sitemap
     */
    public function getSitemap()
    {
        return $this->sitemap;
    }

    /**
     * @return PageContent
     */
    public function getPageContent()
    {
       return $this->pageContent;
    }
}
