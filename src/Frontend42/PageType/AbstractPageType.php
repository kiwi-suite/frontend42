<?php
namespace Frontend42\PageType;

use Cocur\Slugify\Slugify;
use Frontend42\Model\Page as PageModel;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeContent;
use Frontend42\PageType\PageTypeInterface;

abstract class AbstractPageType implements PageTypeInterface
{
    /**
     * @param Sitemap $sitemap
     * @return null
     */
    public function prepareForAdd(Sitemap $sitemap)
    {
        $sitemap->setTerminal(false);
    }

    /**
     * @param PageModel $page
     * @return mixed
     */
    public function deletePage(PageModel $page)
    {
    }

    /**
     * @param PageTypeContent $content
     * @param PageModel $page
     * @param $approved
     * @return mixed
     */
    public function savePage(PageTypeContent $content, PageModel $page, $approved)
    {
        if ($approved === false) {
            return;
        }
        $slug = "";
        $name = $content->getParam("name");

        if (!empty($name)) {
            $slugify = new Slugify();
            $slug = $slugify->slugify($name);
        }

        $publishedFrom = $content->getParam('publishedFrom');
        $publishedFrom = empty($publishedFrom) ? null : \DateTime::createFromFormat('Y-m-d H:i', $publishedFrom);
        $publishedUntil = $content->getParam('publishedUntil');
        $publishedUntil = empty($publishedUntil) ? null : \DateTime::createFromFormat('Y-m-d H:i', $publishedUntil);

        $excludeMenu = ($content->getParam("excludeMenu", "false") == "true") ? true : false;

        $routing = $this->getRouting($content, $page);

        $page->setSlug($slug)
            ->setName($name)
            ->setStatus($content->getParam('status'))
            ->setExcludeMenu($excludeMenu)
            ->setPublishedFrom($publishedFrom)
            ->setPublishedUntil($publishedUntil)
            ->setRoute(json_encode($routing));
    }

    abstract protected function getRouting(PageTypeContent $content, PageModel $page);
}
