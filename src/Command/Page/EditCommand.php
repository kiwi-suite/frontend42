<?php
namespace Frontend42\Command\Page;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Frontend42\Command\PageVersion\ApproveCommand;
use Frontend42\Command\PageVersion\CreateCommand;
use Frontend42\Event\PageEvent;
use Frontend42\Model\Page;
use Frontend42\Model\PageVersion;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Provider\PageTypeProvider;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Json\Json;

class EditCommand extends AbstractCommand
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
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * @var PageTypeInterface
     */
    protected $pageType;

    /**
     * @var User
     */
    protected $updateUser;

    /**
     * @var boolean
     */
    protected $approve = false;

    /**
     * @var PageContent
     */
    protected $pageContent;

    /**
     * @var PageVersion
     */
    protected $pageVersion;

    /**
     * @param Page $page
     * @return EditCommand
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @param int $pageId
     * @return EditCommand
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * @param User $updateUser
     * @return $this
     */
    public function setUpdateUser(User $updateUser)
    {
        $this->updateUser = $updateUser;

        return $this;
    }

    /**
     * @param boolean $approve
     * @return $this
     */
    public function setApprove($approve)
    {
        $this->approve = (boolean) $approve;

        return $this;
    }

    /**
     * @param PageContent $pageContent
     * @return EditCommand
     */
    public function setPageContent($pageContent)
    {
        $this->pageContent = $pageContent;

        return $this;
    }

    protected function preExecute()
    {
        if ($this->pageId > 0) {
            $this->page = $this->getTableGateway(PageTableGateway::class)->selectByPrimary((int) $this->pageId);
        }

        if (empty($this->page)) {
            $this->addError("page", "invalid page");

            return;
        }

        $this->sitemap = $this
            ->getTableGateway(SitemapTableGateway::class)
            ->selectByPrimary($this->page->getSitemapId());

        if (empty($this->sitemap)) {
            $this->addError("sitemap", "invalid sitemap");

            return;
        }

        $this->pageType = $this->getServiceManager()->get(PageTypeProvider::class)->get($this->sitemap->getPageType());

        if (empty($this->updateUser)) {
            $this->addError("user", "invalid user");

            return;
        }

        if (empty($this->pageContent)) {
            $this->addError("pageContent", "invalid pageContent");

            return;
        }

        $this->pageVersion = $this
            ->getSelector(PageVersionSelector::class)
            ->setPageId($this->page->getId())
            ->setVersionName(PageVersionSelector::VERSION_HEAD)
            ->getResult();
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $now = new \DateTime();

        $this
            ->getServiceManager()
            ->get('Frontend42\Page\EventManager')
            ->trigger(
                PageEvent::EVENT_EDIT_PRE,
                $this->page,
                ['sitemap' => $this->sitemap, 'approved' => $this->approve, 'pageContent' => $this->pageContent]
            );

        if ($this->page->hasChanged()) {
            $this->page->setUpdated($now)
                ->setUpdatedBy($this->updateUser->getId());

            $this->getTableGateway(PageTableGateway::class)->update($this->page);
        }

        $this->pageVersion->setContent(Json::encode($this->pageContent->getContent()));

        $pageVersion = $this->pageVersion;

        if ($this->pageVersion->hasChanged("content")) {
            $pageVersion = $this->getCommand(CreateCommand::class)
                ->setPageId($this->page->getId())
                ->setCreatedBy($this->updateUser)
                ->setContent($this->pageContent->getContent())
                ->setPreviousVersion($this->pageVersion)
                ->run();
        }

        if ($this->approve === true) {
            $pageVersion = $this->getCommand(ApproveCommand::class)
                ->setVersion($pageVersion)
                ->run();
        }

        $this
            ->getServiceManager()
            ->get('Frontend42\Page\EventManager')
            ->trigger(
                PageEvent::EVENT_EDIT_POST,
                $this->page,
                ['sitemap' => $this->sitemap, 'approved' => $this->approve, 'pageContent' => $this->pageContent]
            );

        return $pageVersion;

    }
}
