<?php
namespace Frontend42\Navigation;

use Frontend42\Model\Page;
use Frontend42\PageType\PageTypeContent;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\PageTableGateway;
use Zend\Cache\Storage\StorageInterface;
use Zend\Json\Json;

class PageHandler
{
    /**
     * @var StorageInterface
     */
    protected $cache;

    /**
     * @var array
     */
    protected $currentInfo;

    /**
     * @var PageTableGateway
     */
    protected $pageTableGateway;

    /**
     * @var string
     */
    protected $defaultHandle;

    /**
     * @var PageVersionSelector
     */
    protected $pageVersionSelector;

    public function __construct(
        PageTableGateway $pageTableGateway,
        PageVersionSelector $pageVersionSelector,
        StorageInterface $cache
    ) {
        $this->pageTableGateway = $pageTableGateway;
        $this->pageVersionSelector = $pageVersionSelector;
        $this->cache = $cache;
    }

    /**
     * @param string $defaultHandle
     */
    public function setDefaultHandle($defaultHandle)
    {
        $this->defaultHandle = $defaultHandle;
    }


    /**
     * @param int $pageId
     * @param string $version
     */
    public function loadCurrentPage($pageId, $version = PageVersionSelector::VERSION_APPROVED)
    {
        $this->currentInfo = $this->loadByPageId($pageId, $version);
    }

    /**
     * @return array
     */
    public function getCurrentPageInfo()
    {
        return $this->currentInfo;
    }

    /**
     * @param $pageId
     * @param string $version
     * @return array
     * @throws \Exception
     */
    public function loadByPageId($pageId, $version = PageVersionSelector::VERSION_APPROVED)
    {
        $cacheKey = 'page_' . $pageId . '_' . $version;

        if (!$this->cache->hasItem($cacheKey)) {
            $page = $this->pageTableGateway->selectByPrimary((int) $pageId);

            $version = $this->pageVersionSelector
                ->setPageId($page->getId())
                ->setVersionName($version)
                ->getResult();

            $pageContent = new PageTypeContent();
            $pageContent->setContent(Json::decode($version->getContent(), Json::TYPE_ARRAY));

            $this->cache->setItem($cacheKey, [
                'page'      => $page,
                'content'   => $pageContent,
            ]);
        }

        return $this->cache->getItem($cacheKey);
    }

    /**
     * @param $pageId
     * @param string $version
     * @return array
     */
    public function getPageById($pageId, $version = PageVersionSelector::VERSION_APPROVED)
    {
        return $this->loadByPageId($pageId, $version);
    }

    /**
     * @param Page|int $page
     * @return mixed
     * @throws \Exception
     */
    public function getRouteByPage($page)
    {
        if ($page instanceof Page) {
            $page = $page->getId();
        }

        $page = (int) $page;

        $pageMapping = [];
        if ($this->cache->hasItem('pageMapping')) {
            $pageMapping = $this->cache->getItem("pageMapping");
        }

        if (!array_key_exists($page, $pageMapping)) {
            return "";
        }

        return $pageMapping[$page]['route'];
    }

    /**
     * @param string $handle
     * @param string $locale
     * @return string
     * @throws \Exception
     */
    public function getRouteByHandle($handle, $locale)
    {
        $handleMapping = [];
        if ($this->cache->hasItem('handleMapping')) {
            $handleMapping = $this->cache->getItem("handleMapping");
        }

        if (empty($handleMapping[$handle][$locale])) {
            if ($this->defaultHandle !== $handle) {
                return $this->getRouteByHandle($this->defaultHandle, $locale);
            }
            return "";
        }

        return $this->getRouteByPage($handleMapping[$handle][$locale]);
    }

    /**
     * @param string $sitemapId
     * @param string $locale
     * @return string
     * @throws \Exception
     */
    public function getRouteBySitemapId($sitemapId, $locale)
    {
        $sitemapMapping = [];
        if ($this->cache->hasItem('sitemapMapping')) {
            $sitemapMapping = $this->cache->getItem("sitemapMapping");
        }

        if (empty($sitemapMapping[$sitemapId][$locale])) {
            return $this->getRouteByHandle($this->defaultHandle, $locale);
        }

        return $this->getRouteByPage($sitemapMapping[$sitemapId][$locale]);
    }

    /**
     * @param $page
     * @param $locale
     * @return mixed
     * @throws \Exception
     */
    public function getSwitchLanguageRoute($page, $locale)
    {
        if ($page instanceof Page) {
            $page = $page->getId();
        }

        $page = (int) $page;

        $pageMapping = [];
        if ($this->cache->hasItem('pageMapping')) {
            $pageMapping = $this->cache->getItem("pageMapping");
        }

        if (!array_key_exists($page, $pageMapping)) {
            if (!empty($this->defaultHandle)) {
                return $this->getRouteByHandle($this->defaultHandle, $locale);
            }

            throw new \Exception("invalid page and no default handle set");
        }

        if (!array_key_exists($locale, $pageMapping[$page]['locale'])){
            if (!empty($this->defaultHandle)) {
                return $this->getRouteByHandle($this->defaultHandle, $locale);
            }

            return "";
        }

        return $this->getRouteByPage($pageMapping[$page]['locale'][$locale]);
    }
}
