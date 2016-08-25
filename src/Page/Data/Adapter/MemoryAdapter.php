<?php
namespace Frontend42\Page\Data\Adapter;

use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\Page\Data\Storage\MemoryStorage;
use Frontend42\PageType\PageContent\PageContent;
use Psr\Cache\CacheItemPoolInterface;

class MemoryAdapter implements AdapterInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    protected $memoryCache;

    /**
     * MemoryAdapter constructor.
     * @param CacheItemPoolInterface $memoryCache
     */
    public function __construct(CacheItemPoolInterface $memoryCache) {
        $this->memoryCache = $memoryCache;
    }

    /**
     * @param string $itemName
     * @param mixed $default
     * @return mixed
     */
    protected function read($itemName, $default = null)
    {
        $item = $this->memoryCache->getItem($itemName);
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
        return $this->read(MemoryStorage::CACHE_NAME_ROUTING, []);
    }

    /**
     * @param $locale
     * @return array
     */
    public function getNavigation($locale)
    {
        return $this->read(MemoryStorage::CACHE_NAME_NAVIGATION . '/' . $locale, []);
    }

    /**
     * @param int $pageId
     * @return Page|null
     */
    public function getPage($pageId)
    {
        return $this->read(MemoryStorage::CACHE_NAME_PAGE . '/p'.$pageId);
    }

    /**
     * @param int $sitemapId
     * @return Sitemap|null
     */
    public function getSitemap($sitemapId)
    {
        return $this->read(MemoryStorage::CACHE_NAME_SITEMAP . '/s'.$sitemapId);
    }

    /**
     * @param string $handle
     * @param string $locale
     * @return null|int
     */
    public function getHandleMapping($handle, $locale)
    {
        return $this->read(
            MemoryStorage::CACHE_NAME_HANDLE_MAPPING . '/' . $handle . '/' . $locale
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
            MemoryStorage::CACHE_NAME_LOCALE_MAPPING . '/' . (int)$sitemapId . '/' . $locale
        );
    }

    /**
     * @param $pageId
     * @return null|string
     */
    public function getPageRoute($pageId)
    {
        return $this->read(
            MemoryStorage::CACHE_NAME_PAGE_ROUTING . '/' . (int) $pageId
        );
    }

    /**
     * @param mixed $versionId
     * @param int $pageId
     * @return PageContent
     */
    public function getPageContent($versionId, $pageId)
    {
        $this->read(
            MemoryStorage::CACHE_NAME_PAGE_VERSION . '/' . $pageId . '/' . $versionId
        );
    }


    /**
     * @return boolean
     */
    public function canMiss()
    {
        return true;
    }
}
