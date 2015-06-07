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
     * @param array $content
     * @param PageModel $page
     * @return mixed
     */
    public function savePage(array $content, PageModel $page);
}
