<?php
namespace Frontend42\Command\PageVersion;

use Core42\Command\AbstractCommand;
use Core42\Stdlib\DateTime;
use Frontend42\Event\PageEvent;
use Frontend42\Model\Page;
use Frontend42\Model\PageContent;
use Frontend42\Model\PageVersion;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\PageVersionTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;

class ApproveCommand extends AbstractCommand
{
    /**
     * @var int
     */
    protected $versionId;

    /**
     * @var PageVersion
     */
    protected $version;

    /**
     * @param int $versionId
     * @return ApproveCommand
     */
    public function setVersionId($versionId)
    {
        $this->versionId = $versionId;
        return $this;
    }

    /**
     * @param PageVersion $version
     * @return ApproveCommand
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function preExecute()
    {
        if ((int) $this->versionId > 0) {
            $this->version = $this
                ->getTableGateway(PageVersionTableGateway::class)
                ->selectByPrimary((int) $this->versionId);
        }

        if (empty($this->version)) {
            $this->addError("version", "invalid version");

            return;
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $this
            ->getTableGateway(PageVersionTableGateway::class)
            ->update(['approved' => null], ['pageId' => $this->version->getPageId()]);

        $this->version->setApproved(new DateTime());

        $this->getTableGateway(PageVersionTableGateway::class)->update($this->version);

        /** @var Page $page */
        $page = $this->getTableGateway(PageTableGateway::class)->selectByPrimary($this->version->getPageId());

        /** @var Sitemap $sitemap */
        $sitemap = $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary($page->getSitemapId());

        /** @var PageTypeInterface $pageType */
        $pageType = $this->getServiceManager()->get(PageTypePluginManager::class)->get($sitemap->getPageType());

        /** @var PageContent $pageContent */
        $pageContent = $pageType->getPageContent($this->version->getContent(), $page);

        $this
            ->getServiceManager()
            ->get('Frontend42\Page\EventManager')
            ->trigger(
                PageEvent::EVENT_APPROVED,
                $page,
                ['sitemap' => $sitemap, 'approved' => true, 'pageContent' => $pageContent]
            );

        $this->getTableGateway(PageTableGateway::class)->update($page);

        return $this->version;
    }
}
