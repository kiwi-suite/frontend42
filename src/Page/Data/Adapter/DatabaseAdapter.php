<?php
namespace Frontend42\Page\Data\Adapter;


use Frontend42\Command\Frontend\BuildIndexCommand;
use Frontend42\Model\Page;
use Frontend42\Model\PageVersion;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;
use Frontend42\PageType\Provider\PageTypeProvider;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;

class DatabaseAdapter implements AdapterInterface
{
    /**
     * @var PageTableGateway
     */
    protected $pageTableGateway;

    /**
     * @var SitemapTableGateway
     */
    protected $sitemapTableGateway;

    /**
     * @var BuildIndexCommand
     */
    protected $buildIndexCommand;

    /**
     * @var PageVersionSelector
     */
    protected $pageVersionSelector;

    /**
     * @var PageTypeProvider
     */
    protected $pageTypeProvider;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var array
     */
    protected $data = [];

    public function __construct(
        PageTableGateway $pageTableGateway,
        SitemapTableGateway $sitemapTableGateway,
        PageVersionSelector $pageVersionSelector,
        BuildIndexCommand $buildIndexCommand,
        PageTypeProvider $pageTypeProvider
    ) {
        $this->pageTableGateway = $pageTableGateway;
        $this->sitemapTableGateway = $sitemapTableGateway;
        $this->pageVersionSelector = $pageVersionSelector;
        $this->buildIndexCommand = $buildIndexCommand;
        $this->pageTypeProvider = $pageTypeProvider;
    }

    /**
     * @return boolean
     */
    public function canMiss()
    {
        return false;
    }

    /**
     * @return array
     */
    public function getRouting()
    {
        $this->initialize();

        if (isset($this->data['routing'])) {
            return $this->data['routing'];
        }
    }

    /**
     * @param $locale
     * @return array
     */
    public function getNavigation($locale)
    {
        $this->initialize();

        if (isset($this->data['navigation'][$locale])) {
            return $this->data['navigation'][$locale];
        }
    }

    /**
     * @param int $pageId
     * @return Page|null
     */
    public function getPage($pageId)
    {
        return $this->pageTableGateway->selectByPrimary($pageId);
    }

    /**
     * @param int $sitemapId
     * @return Sitemap|null
     */
    public function getSitemap($sitemapId)
    {
        return $this->sitemapTableGateway->selectByPrimary($sitemapId);
    }

    /**
     * @param string $handle
     * @param string $locale
     * @return null|int
     */
    public function getHandleMapping($handle, $locale)
    {
        if (!strlen($handle)) {
            return;
        }

        $sitemap = $this->sitemapTableGateway->select(['handle' => $handle]);
        if ($sitemap->count() == 0) {
            return;
        }
        $sitemap = $sitemap->current();

        $page = $this->pageTableGateway->select([
            'sitemapId' => $sitemap->getId(),
            'locale'    => $locale,
        ]);
        if ($page->count() == 0) {
            return;
        }

        return $page->current()->getId();
    }

    /**
     * @param int $sitemapId
     * @param string $locale
     * @return int|null
     */
    public function getLocaleMapping($sitemapId, $locale)
    {
        $sitemap = $this->sitemapTableGateway->selectByPrimary($sitemapId);
        if (empty($sitemap)) {
            return;
        }

        $page = $this->pageTableGateway->select([
            'sitemapId' => $sitemap->getId(),
            'locale'    => $locale,
        ]);
        if ($page->count() == 0) {
            return;
        }

        return $page->current()->getId();
    }

    /**
     * @param $pageId
     * @return null|string
     */
    public function getPageRoute($pageId)
    {
        $this->initialize();

        if (isset($this->data['pageRoute'][$pageId])) {
            return $this->data['pageRoute'][$pageId];
        }
    }

    /**
     *
     */
    protected function initialize()
    {
        if ($this->initialized === true) {
            return;
        }

        $this->initialized = true;

        $this->data = $this->buildIndexCommand
            ->setCaching(false)
            ->enableResult(true)
            ->run();
    }

    /**
     * @param mixed $versionId
     * @param int $pageId
     * @return PageContent
     */
    public function getPageContent($versionId, $pageId)
    {
        $page = $this->getPage($pageId);
        if (empty($page)) {
            return;
        }
        $sitemap = $this->getSitemap($page->getSitemapId());
        if (empty($sitemap)) {
            return;
        }

        $pageVersion = $this->pageVersionSelector
            ->setVersionName($versionId)
            ->setPageId($page->getId())
            ->getResult();

        $pageContent = $this->pageTypeProvider->get($sitemap->getPageType())->getPageContent();
        $pageContent->setContent($pageVersion->getContent());

        return $pageContent;
    }


}
