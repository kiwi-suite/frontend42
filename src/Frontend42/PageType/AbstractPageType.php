<?php
namespace Frontend42\PageType;

use Cocur\Slugify\Slugify;
use Frontend42\Command\Keyword\RefreshPageKeywordsCommand;
use Frontend42\Model\Page as PageModel;
use Frontend42\Model\Sitemap;
use Frontend42\Navigation\PageHandler;
use Frontend42\PageType\PageTypeContent;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\TableGateway\PageKeywordTableGateway;

abstract class AbstractPageType implements PageTypeInterface
{
    /**
     * @var RefreshPageKeywordsCommand
     */
    protected $refreshPageKeywordsCommand;

    /**
     * @var PageHandler
     */
    protected $pageHandler;

    /**
     * @param RefreshPageKeywordsCommand $refreshPageKeywordsCommand
     */
    public function setKeywordCommand(RefreshPageKeywordsCommand $refreshPageKeywordsCommand)
    {
        $this->refreshPageKeywordsCommand = $refreshPageKeywordsCommand;
    }

    /**
     * @param PageHandler $pageHandler
     */
    public function setPageHandler(PageHandler $pageHandler)
    {
        $this->pageHandler = $pageHandler;
    }

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
    public function savePage(
        PageTypeContent $content,
        PageModel $page,
        $approved
    ) {
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
        $publishedFrom = empty($publishedFrom) ? null : new \DateTime($publishedFrom);
        $publishedUntil = $content->getParam('publishedUntil');
        $publishedUntil = empty($publishedUntil) ? null : new \DateTime($publishedUntil);
        
        $excludeMenu = ($content->getParam("excludeMenu", "false") == "true") ? true : false;

        $routing = $this->getRouting($content, $page);

        $page->setSlug($slug)
            ->setName($name)
            ->setStatus($content->getParam('status'))
            ->setExcludeMenu($excludeMenu)
            ->setPublishedFrom($publishedFrom)
            ->setPublishedUntil($publishedUntil)
            ->setRoute(json_encode($routing));

        $keywords = trim($content->getParam('keywords'));
        $keywords = (empty($keywords)) ? [] : explode(',', $keywords);
        $this->refreshPageKeywordsCommand->setPageId($page->getId())->setKeywords($keywords)->run();
    }

    /**
     * @param \Frontend42\PageType\PageTypeContent $content
     * @param PageModel $page
     * @return mixed
     */
    abstract protected function getRouting(PageTypeContent $content, PageModel $page);

    /**
     * @param PageModel $page
     * @param \Frontend42\PageType\PageTypeContent $content
     * @return array|false
     */
    public function getRoutingParams(PageModel $page, PageTypeContent $content)
    {
        return false;
    }
}
