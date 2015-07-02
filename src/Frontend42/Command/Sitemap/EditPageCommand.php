<?php
namespace Frontend42\Command\Sitemap;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Frontend42\Model\Page;
use Frontend42\Model\PageVersion;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeContent;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\Selector\PageVersionSelector;
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
     * @var User
     */
    protected $createdUser;

    /**
     * @var PageVersion
     */
    protected $pageVersion;

    /**
     * @var PageTypeInterface
     */
    protected $pageType;

    /**
     * @var PageTypeContent
     */
    protected $pageTypeContent;

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

    /**
     * @param User $createdUser
     * @return $this
     */
    public function setCreatedUser(User $createdUser)
    {
        $this->createdUser = $createdUser;

        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function preExecute()
    {
        if ((int) $this->pageId > 0) {
            $this->page = $this->getTableGateway('Frontend42\Page')->selectByPrimary((int) $this->pageId);
        }

        if (empty($this->page)) {
            $this->addError("page", "invalid page");

            return;
        }

        $this->sitemap = $this->getTableGateway('Frontend42\Sitemap')->selectByPrimary($this->page->getSitemapId());

        if (empty($this->sitemap)) {
            $this->addError("sitemap", "can't load sitemap");

            return;
        }

        $this->pageVersion = $this
            ->getSelector('Frontend42\PageVersion')
            ->setPageId($this->page->getId())
            ->setVersionName(PageVersionSelector::VERSION_HEAD)
            ->getResult();

        $this->pageType = $this->getServiceManager()
            ->get('Frontend42\PageTypeProvider')
            ->getPageType($this->sitemap->getPageType());

        $this->pageTypeContent = $this->getServiceManager()->get('Frontend42\PageTypeContent');
        $this->pageTypeContent->setRawContent($this->content);
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $this->pageVersion->setContent(Json::encode($this->content));

        if (!$this->pageVersion->hasChanged("content")) {
            return $this->pageVersion;
        }

        $pageVersion = new PageVersion();
        $pageVersion->setVersionId($this->pageVersion->getVersionId() + 1)
            ->setContent($this->pageVersion->getContent())
            ->setPageId($this->page->getId())
            ->setCreated(new \DateTime())
            ->setCreatedBy($this->createdUser->getId());

        $this->getTableGateway('Frontend42\PageVersion')->insert($pageVersion);

        $this->pageType->savePage($this->pageTypeContent, $this->page);

        $this->getTableGateway('Frontend42\Page')->update($this->page);

        $this->getCommand('Frontend42\Router\CreateRouteConfig')->run();

        return $pageVersion;
    }
}
