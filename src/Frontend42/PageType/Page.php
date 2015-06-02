<?php
namespace Frontend42\PageType;

use Frontend42\Model\Sitemap;

class Page implements PageTypeInterface
{
    /**
     * @param Sitemap $sitemap
     * @param \Frontend42\Model\Page $page
     * @return null
     */
    public function addPage(Sitemap $sitemap, \Frontend42\Model\Page $page)
    {

    }

    /**
     * @param Sitemap $sitemap
     * @return null
     */
    public function prepareForAdd(Sitemap $sitemap)
    {
        $sitemap->setHandle(null)
            ->setTerminal(false);
    }
}
