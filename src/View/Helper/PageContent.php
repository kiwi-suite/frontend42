<?php
namespace Frontend42\View\Helper;

use Core42\View\Helper\Proxy;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\Selector\ApprovedPageContentSelector;
use Frontend42\Selector\PageSelector;
use Frontend42\Selector\SitemapSelector;

class PageContent extends Proxy
{
    /**
     * @var PageSelector
     */
    protected $pageSelector;

    /**
     * @var SitemapSelector
     */
    protected $sitemapSelector;

    /**
     * @var PageTypePluginManager
     */
    protected $pageTypePluginManager;

    /**
     * @var ApprovedPageContentSelector
     */
    protected $approvedPageContentSelector;

    /**
     * Page constructor.
     * @param PageSelector $pageSelector
     * @param SitemapSelector $sitemapSelector
     * @param PageTypePluginManager $pageTypePluginManager
     * @param ApprovedPageContentSelector $approvedPageContentSelector
     */
    public function __construct(
        PageSelector $pageSelector,
        SitemapSelector $sitemapSelector,
        PageTypePluginManager $pageTypePluginManager,
        ApprovedPageContentSelector $approvedPageContentSelector
    ) {
        $this->pageSelector = $pageSelector;
        $this->sitemapSelector = $sitemapSelector;
        $this->pageTypePluginManager = $pageTypePluginManager;
        $this->approvedPageContentSelector = $approvedPageContentSelector;
    }

    public function __invoke($pageId = null)
    {
        if ($pageId !== null) {
            $page = $this->pageSelector->setPageId((int)$pageId)->getResult();
            $sitemap = $this->sitemapSelector->setSitemapId($page->getSitemapId())->getResult();

            $pageContent = $this->approvedPageContentSelector->setPageId($page->getId())->getResult();
            $pageContent = $this->pageTypePluginManager->get($sitemap->getPageType())->mutate($pageContent);

            $this->object = $pageContent;
        }

        return $this;
    }
}
