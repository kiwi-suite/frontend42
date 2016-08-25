<?php
namespace Frontend42\Page\Data\Storage;

use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;
use Psr\Cache\CacheItemPoolInterface;

class MemoryStorage implements StorageInterface
{
    const CACHE_NAME_ROUTING = "routing";
    const CACHE_NAME_NAVIGATION = "navigation";
    const CACHE_NAME_PAGE = "page";
    const CACHE_NAME_SITEMAP = "sitemap";
    const CACHE_NAME_HANDLE_MAPPING = "handlemapping";
    const CACHE_NAME_LOCALE_MAPPING = "localemapping";
    const CACHE_NAME_PAGE_ROUTING = "pagerouting";
    const CACHE_NAME_PAGE_VERSION = "pageversion";


    /**
     * @var CacheItemPoolInterface
     */
    protected $memoryCache;

    /**
     * CacheStorage constructor.
     * @param CacheItemPoolInterface $memoryCache
     */
    public function __construct(
        CacheItemPoolInterface $memoryCache
    ) {
        $this->memoryCache = $memoryCache;
    }

    /**
     * @param $name
     * @param $value
     */
    protected function write($name, $value)
    {
        $item = $this->memoryCache->getItem($name);
        $item->set($value);
        $this->memoryCache->save($item);
    }

    /**
     * @param array $routing
     * @return mixed
     */
    public function writeRouting(array $routing)
    {
        $this->write(self::CACHE_NAME_ROUTING, $routing);
    }

    /**
     * @param array $navigation
     * @param $locale
     * @return mixed
     */
    public function writeNavigation(array $navigation, $locale)
    {
        $this->write(self::CACHE_NAME_NAVIGATION . '/' . $locale, $navigation);
    }

    /**
     * @param Page $page
     * @return mixed
     */
    public function writePage(Page $page)
    {
        $this->write(self::CACHE_NAME_PAGE . '/'.$page->getId(), $page);
    }

    /**
     * @param Sitemap $sitemap
     * @return mixed
     */
    public function writeSitemap(Sitemap $sitemap)
    {
        $this->write(self::CACHE_NAME_SITEMAP . '/'.$sitemap->getId(), $sitemap);
    }

    /**
     * @param int $pageId
     * @param string $route
     */
    public function writePageRoute($pageId, $route)
    {
        $this->write(self::CACHE_NAME_PAGE_ROUTING . '/' . $pageId, $route);
    }

    /**
     * @param int $sitemapId
     * @param string $locale
     * @param int $pageId
     */
    public function writeLocaleMapping($sitemapId, $locale, $pageId)
    {
        $this->write(
            self::CACHE_NAME_LOCALE_MAPPING . '/' . $sitemapId . '/' . $locale,
            $pageId
        );
    }

    /**
     * @param string $handle
     * @param string $locale
     * @param int $pageId
     */
    public function writeHandleMapping($handle, $locale, $pageId)
    {
        if (!strlen($handle)) {
            return;
        }
        $this->write(
            self::CACHE_NAME_HANDLE_MAPPING . '/' . $handle . '/' . $locale,
            $pageId
        );
    }

    /**
     * @param int $versionId
     * @param int $pageId
     * @param PageContent $content
     */
    public function writePageContent($versionId, $pageId, PageContent $content)
    {
        $this->write(
            self::CACHE_NAME_PAGE_VERSION . '/' . $pageId . '/' . $versionId,
            $content
        );
    }
}
