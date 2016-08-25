<?php
namespace Frontend42\Page\Data\Storage;

use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;

/**
 * @package Frontend42\Page\Data\Storage
 */
interface StorageInterface
{
    /**
     * @param array $routing
     */
    public function writeRouting(array $routing);

    /**
     * @param array $navigation
     * @param $locale
     * @return
     */
    public function writeNavigation(array $navigation, $locale);

    /**
     * @param Page $page
     */
    public function writePage(Page $page);

    /**
     * @param Sitemap $sitemap
     */
    public function writeSitemap(Sitemap $sitemap);

    /**
     * @param int $pageId
     * @param string $route
     */
    public function writePageRoute($pageId, $route);

    /**
     * @param int $sitemapId
     * @param string $locale
     * @param int $pageId
     */
    public function writeLocaleMapping($sitemapId, $locale, $pageId);

    /**
     * @param string $handle
     * @param string $locale
     * @param int $pageId
     */
    public function writeHandleMapping($handle, $locale, $pageId);

    /**
     * @param string $versionId
     * @param int $pageId
     * @param PageContent $content
     */
    public function writePageContent($versionId, $pageId, PageContent $content);
}
