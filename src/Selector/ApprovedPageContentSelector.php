<?php
namespace Frontend42\Selector;

use Core42\Selector\AbstractSelector;
use Core42\Selector\CacheAbleTrait;
use Frontend42\Model\Page;
use Frontend42\Model\PageVersion;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Service\PageTypePluginManager;

class ApprovedPageContentSelector extends AbstractSelector
{
    use CacheAbleTrait;

    /**
     * @var int
     */
    protected $pageId;

    /**
     * @param int $pageId
     * @return $this
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * @return string
     */
    protected function getCacheName()
    {
        return "pageContent";
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return "page" . $this->pageId;
    }

    /**
     * @return mixed
     */
    protected function getUncachedResult()
    {
        /** @var Page $page */
        $page = $this->getSelector(PageSelector::class)->setPageId($this->pageId)->getResult();

        /** @var Sitemap $sitemap */
        $sitemap = $this->getSelector(SitemapSelector::class)->setSitemapId($page->getSitemapId())->getResult();

        /** @var PageTypeInterface $pageType */
        $pageType = $this->getServiceManager()->get(PageTypePluginManager::class)->get($sitemap->getPageType());

        /** @var PageVersionSelector $selector */
        $selector = $this->getSelector(PageVersionSelector::class);

        /** @var PageVersion $pageVersion */
        $pageVersion = $selector
            ->setPageId($this->pageId)
            ->setVersionId(PageVersionSelector::VERSION_APPROVED)
            ->getResult();


        return $pageType->getPageContent($pageVersion->getContent(), $page);
    }
}
