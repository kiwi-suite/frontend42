<?php
namespace Frontend42\PageType;

use Frontend42\Model\Page as PageModel;
use Frontend42\Model\Sitemap;

interface PageTypeInterface
{
    /**
     * @param Sitemap $sitemap
     * @return null
     */
    public function prepareForAdd(Sitemap $sitemap);

    /**
     * @param Sitemap $sitemap
     * @param PageModel $page
     * @return null
     */
    public function addPage(Sitemap $sitemap, PageModel $page);

}
