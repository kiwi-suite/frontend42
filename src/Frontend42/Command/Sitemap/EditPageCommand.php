<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Zend\Json\Json;

class EditPageCommand extends AbstractCommand
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * @var int
     */
    protected $pageId;

    /**
     * @var mixed
     */
    protected $content;

    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * @param Page $page
     * @return $this
     */
    public function setPage(Page $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @param int $pageId
     * @return $this
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    protected function preExecute()
    {
        if ((int) $this->pageId > 0) {
            $this->page = $this->getTableGateway('Frontend42\Page')->selectByPrimary((int) $this->pageId);
        }

        if (empty($this->page)) {
            $this->addError("page", "invalid page");

            return;
        }

        $this->content = Json::encode($this->content);

        $this->sitemap = $this->getTableGateway('Frontend42\Sitemap')->selectByPrimary($this->page->getSitemapId());

        if (empty($this->sitemap)) {
            $this->addError("sitemap")
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {

    }
}
