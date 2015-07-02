<?php
namespace Frontend42\PageType;

use Cocur\Slugify\Slugify;
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
     * @param PageTypeContent $content
     * @param PageModel $page
     * @return mixed
     */
    public function savePage(PageTypeContent $content, PageModel $page)
    {
        $name = $content->getElement("name");

        $publishedFrom = $content->getElement('publishedFrom');
        $publishedFrom = empty($publishedFrom) ? null : \DateTime::createFromFormat('Y-m-d H:i', $publishedFrom);
        $publishedUntil = $content->getElement('publishedUntil');
        $publishedUntil = empty($publishedUntil) ? null : \DateTime::createFromFormat('Y-m-d H:i', $publishedUntil);

        $slugify = new Slugify();
        $page->setSlug($slugify->slugify($name))
            ->setName($name)
            ->setStatus($content->getElement('status'))
            ->setPublishedFrom($publishedFrom)
            ->setPublishedUntil($publishedUntil);
    }
}
