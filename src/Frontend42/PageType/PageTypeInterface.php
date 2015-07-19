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
     * @param PageTypeContent $content
     * @param PageModel $page
     * @param $approved
     * @return mixed
     */
    public function savePage(PageTypeContent $content, PageModel $page, $approved);
}
