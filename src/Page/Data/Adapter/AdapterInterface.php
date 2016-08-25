<?php
namespace Frontend42\Page\Data\Adapter;

use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;

interface AdapterInterface
{
    /**
     * @return boolean
     */
    public function canMiss();

    /**
     * @return array
     */
    public function getRouting();

    /**
     * @param $locale
     * @return array
     */
    public function getNavigation($locale);

    /**
     * @param int $pageId
     * @return Page|null
     */
    public function getPage($pageId);

    /**
     * @param int $sitemapId
     * @return Sitemap|null
     */
    public function getSitemap($sitemapId);

    /**
     * @param string $handle
     * @param string $locale
     * @return null|int
     */
    public function getHandleMapping($handle, $locale);

    /**
     * @param int $sitemapId
     * @param string $locale
     * @return int|null
     */
    public function getLocaleMapping($sitemapId, $locale);

    /**
     * @param $pageId
     * @return null|string
     */
    public function getPageRoute($pageId);

    /**
     * @param mixed $versionId
     * @param int $pageId
     * @return PageContent
     */
    public function getPageContent($versionId, $pageId);
}
