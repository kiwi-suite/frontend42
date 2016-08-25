<?php
namespace Frontend42\Page\Data\Storage;

use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;
use Psr\Cache\CacheItemPoolInterface;

class CacheStorage implements StorageInterface
{
    const CACHE_NAME_ROUTING = "routing";
    const CACHE_NAME_NAVIGATION = "navigation";
    const CACHE_NAME_HANDLE_MAPPING = "handlemapping";
    const CACHE_NAME_LOCALE_MAPPING = "localemapping";
    const CACHE_NAME_PAGE_ROUTING = "pagerouting";

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

    /**
     * CacheStorage constructor.
     * @param CacheItemPoolInterface $frontendCache
     * @param CacheItemPoolInterface $sitemapCache
     * @param CacheItemPoolInterface $pageCache
     * @param CacheItemPoolInterface $versionCache
     */
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
     * @param $name
     * @param $value
     */
    protected function write(CacheItemPoolInterface $cache, $name, $value)
    {
        $item = $cache->getItem($name);
        $item->set($value);
        $cache->save($item);
    }

    /**
     * @param array $routing
     */
    public function writeRouting(array $routing)
    {
        $this->write($this->frontendCache, self::CACHE_NAME_ROUTING, $routing);
    }

    /**
     * @param array $navigation
     */
    public function writeNavigation(array $navigation, $locale)
    {
        $this->write($this->frontendCache, self::CACHE_NAME_NAVIGATION . '/' . $locale, $navigation);
    }

    /**
     * @param Page $page
     */
    public function writePage(Page $page)
    {
        $this->write($this->pageCache, 'p'.$page->getId(), $page);
    }

    /**
     * @param Sitemap $sitemap
     */
    public function writeSitemap(Sitemap $sitemap)
    {
        $this->write($this->sitemapCache, 's'.$sitemap->getId(), $sitemap);
    }

    /**
     * @param int $pageId
     * @param string $route
     */
    public function writePageRoute($pageId, $route)
    {
        $this->write($this->frontendCache, self::CACHE_NAME_PAGE_ROUTING . '/' . $pageId, $route);
    }

    /**
     * @param int $sitemapId
     * @param string $locale
     * @param int $pageId
     */
    public function writeLocaleMapping($sitemapId, $locale, $pageId)
    {
        $this->write(
            $this->frontendCache,
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
            $this->frontendCache,
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
            $this->versionCache,
            $pageId . '/' . $versionId,
            $content
        );
    }
}
