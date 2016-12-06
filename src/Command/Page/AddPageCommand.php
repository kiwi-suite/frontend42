<?php
namespace Frontend42\Command\Page;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Core42\Stdlib\DateTime;
use Frontend42\Command\PageVersion\ApproveCommand;
use Frontend42\Command\PageVersion\CreateCommand;
use Frontend42\Event\PageEvent;
use Frontend42\Model\Page;
use Frontend42\Model\PageContent;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\TableGateway\PageTableGateway;

class AddPageCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * @var User
     */
    protected $user;

    /**
     * @param string $name
     * @return AddPageCommand
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $locale
     * @return AddPageCommand
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param Sitemap $sitemap
     * @return AddPageCommand
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
     * @return mixed
     */
    protected function execute()
    {
        /** @var PageTypeInterface $pageType */
        $pageType = $this->getServiceManager()->get(PageTypePluginManager::class)->get($this->sitemap->getPageType());

        /** @var PageContent $pageContent */
        $pageContent = $pageType->getPageContent();
        $pageContent->setName($this->name)
            ->setStatus(Page::STATUS_OFFLINE);

        $dateTime = new DateTime();

        $page = new Page();
        $page->setLocale($this->locale)
            ->setSitemapId($this->sitemap->getId())
            ->setUpdated($dateTime)
            ->setCreated($dateTime);

        $this
            ->getServiceManager()
            ->get('Frontend42\Page\EventManager')
            ->trigger(
                PageEvent::EVENT_ADD_PRE,
                $page,
                ['sitemap' => $this->sitemap, 'approved' => true, 'pageContent' => $pageContent]
            );

        $this->getTableGateway(PageTableGateway::class)->insert($page);

        /** @var CreateCommand $cmd */
        $cmd = $this->getCommand(CreateCommand::class);
        $cmd->setUser($this->user)
            ->setPageId($page->getId())
            ->setPageContent($pageContent);

        $version = $cmd->run();

        /** @var ApproveCommand $cmd */
        $cmd = $this->getCommand(ApproveCommand::class);
        $cmd->setVersion($version)
            ->run();

        $this
            ->getServiceManager()
            ->get('Frontend42\Page\EventManager')
            ->trigger(
                PageEvent::EVENT_ADD_POST,
                $page,
                ['sitemap' => $this->sitemap, 'approved' => true, 'pageContent' => $pageContent]
            );

        return $page;
    }
}
