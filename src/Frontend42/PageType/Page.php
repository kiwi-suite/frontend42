<?php
namespace Frontend42\PageType;

use Frontend42\Model\Page as PageModel;
use Frontend42\Model\Sitemap;

class Page implements PageTypeInterface
{
    /**
     * @param Sitemap $sitemap
     * @return null
     */
    public function prepareForAdd(Sitemap $sitemap)
    {
        $sitemap->setHandle(null)
            ->setTerminal(false);
    }

    /**
     * @param array $content
     * @param PageModel $page
     * @return mixed
     */
    public function savePage(array $content, PageModel $page)
    {

    }
}
