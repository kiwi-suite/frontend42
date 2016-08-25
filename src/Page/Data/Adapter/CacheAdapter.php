<?php
namespace Frontend42\Page\Data\Adapter;

use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\Page\Data\Storage\CacheStorage;
use Frontend42\PageType\PageContent\PageContent;
use Psr\Cache\CacheItemPoolInterface;

class CacheAdapter implements AdapterInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    protected $frontendCache;

    /**
     * @var CacheItemPoolInterface
     */
    protected $sitemapCache;

    /**
     * @var CacheItemPoolInterface
     */
    protected $pageCache;

    /**
     * @var CacheItemPoolInterface
     */
    protected $versionCache;

    public function __construct(
        CacheItemPoolInterface $frontendCache,
        CacheItemPoolInterface $sitemapCache,
        CacheItemPoolInterface $pageCache,
        CacheItemPoolInterface $versionCache
    ) {
        $this->frontendCache = $frontendCache;
        $this->sitemapCache = $sitemapCache;
        $this->pageCache = $pageCache;
        $this->versionCache = $versionCache;
    }

    /**
     * @param CacheItemPoolInterface $cache
     * @param string $itemName
     * @param mixed $default
     * @return mixed
     */
    protected function read(CacheItemPoolInterface $cache, $itemName, $default = null)
    {
        $item = $cache->getItem($itemName);
        $value = $item->get();
        if ($item->isHit()) {
            return $value;
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getRouting()
    {
        return $this->read($this->frontendCache, CacheStorage::CACHE_NAME_ROUTING);
    }

    /**
     * @param $locale
     * @return array
     */
    public function getNavigation($locale)
    {
        return $this->read($this->frontendCache, CacheStorage::CACHE_NAME_NAVIGATION . '/' . $locale);
    }

    /**
     * @param int $pageId
     * @return Page|null
     */
    public function getPage($pageId)
    {
        return $this->read($this->pageCache, $pageId);
    }

    /**
     * @param int $sitemapId
     * @return Sitemap|null
     */
    public function getSitemap($sitemapId)
    {
        return $this->read($this->sitemapCache, $sitemapId);
    }

    /**
     * @param string $handle
     * @param string $locale
     * @return null|int
     */
    public function getHandleMapping($handle, $locale)
    {
        return $this->read(
            $this->frontendCache,
            CacheStorage::CACHE_NAME_HANDLE_MAPPING . '/' . $handle . '/' . $locale
        );
    }

    /**
     * @param int $sitemapId
     * @param string $locale
     * @return int|null
     */
    public function getLocaleMapping($sitemapId, $locale)
    {
        return $this->read(
            $this->frontendCache,
            CacheStorage::CACHE_NAME_LOCALE_MAPPING . '/' . $sitemapId . '/' . $locale
        );
    }

    /**
     * @param $pageId
     * @return null|string
     */
    public function getPageRoute($pageId)
    {
        return $this->read(
            $this->frontendCache,
            CacheStorage::CACHE_NAME_PAGE_ROUTING. '/' . $pageId
        );
    }

    /**
     * @param mixed $versionId
     * @param int $pageId
     * @return PageContent
     */
    public function getPageContent($versionId, $pageId)
    {
        return $this->read(
            $this->versionCache,
            $pageId . '/' . $versionId
        );
    }


    /**
     * @return boolean
     */
    public function canMiss()
    {
        return false;
    }
}
