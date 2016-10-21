<?php
namespace Frontend42\Command\Page;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Frontend42\Command\PageVersion\ApproveCommand;
use Frontend42\Command\PageVersion\CreateCommand;
use Frontend42\Event\PageEvent;
use Frontend42\Model\Page;
use Frontend42\Model\PageContent;
use Frontend42\Model\PageVersion;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\TableGateway\PageTableGateway;

class EditPageCommand extends AbstractCommand
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $content;

    /**
     * @var bool
     */
    protected $approve = true;

    /**
     * @var PageVersion
     */
    protected $currentVersion;

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
     * @param Sitemap $sitemap
     * @return $this
     */
    public function setSitemap(Sitemap $sitemap)
    {
        $this->sitemap = $sitemap;

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param array $content
     * @return $this
     */
    public function setContent(array $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param bool $approve
     * @return $this
     */
    public function setApprove($approve)
    {
        $this->approve = $approve;

        return $this;
    }

    /**
     * @param PageVersion $currentVersion
     * @return $this
     */
    public function setCurrentVersion(PageVersion $currentVersion)
    {
        $this->currentVersion = $currentVersion;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        /** @var PageTypeInterface $pageType */
        $pageType = $this->getServiceManager()->get(PageTypePluginManager::class)->get($this->sitemap->getPageType());

        /** @var PageContent $pageContent */
        $pageContent = $pageType->getPageContent($this->content, $this->page);

        $this
            ->getServiceManager()
            ->get('Frontend42\Page\EventManager')
            ->trigger(
                PageEvent::EVENT_EDIT_PRE,
                $this->page,
                ['sitemap' => $this->sitemap, 'approved' => $this->approve, 'pageContent' => $pageContent]
            );

        $this->getTableGateway(PageTableGateway::class)->update($this->page);

        /** @var CreateCommand $cmd */
        $cmd = $this->getCommand(CreateCommand::class);
        $cmd->setUser($this->user)
            ->setPageId($this->page->getId())
            ->setPreviousVersion($this->currentVersion)
            ->setPageContent($pageContent);

        $version = $cmd->run();

        if ($this->approve === true) {
            /** @var ApproveCommand $cmd */
            $cmd = $this->getCommand(ApproveCommand::class);
            $cmd->setVersion($version)
                ->run();
        }

        $this
            ->getServiceManager()
            ->get('Frontend42\Page\EventManager')
            ->trigger(
                PageEvent::EVENT_EDIT_POST,
                $this->page,
                ['sitemap' => $this->sitemap, 'approved' => $this->approve, 'pageContent' => $pageContent]
            );

        return $this->page;
    }
}
