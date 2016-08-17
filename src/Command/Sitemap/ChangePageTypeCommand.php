<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Core42\Db\ResultSet\ResultSet;
use Frontend42\Event\PageEvent;
use Frontend42\Model\Page;
use Frontend42\Model\PageVersion;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Provider\PageTypeProvider;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\PageVersionTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Json\Json;

class ChangePageTypeCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $sitemapId;

    /**
     * @var string
     */
    protected $pageType;

    /**
     * @var ResultSet
     */
    protected $pages;

    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * @var int
     */
    protected $createdBy;

    /**
     * @param string $sitemapId
     * @return $this
     */
    public function setSitemapId($sitemapId)
    {
        $this->sitemapId = $sitemapId;

        return $this;
    }

    /**
     * @param string $pageType
     * @return $this
     */
    public function setPageType($pageType)
    {
        $this->pageType = $pageType;

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
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function preExecute()
    {
        if ($this->sitemapId > 0) {
            $this->sitemap = $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary((int) $this->sitemapId);
        }

        if (empty($this->sitemap)) {
            $this->addError("sitemap", "invalid sitemap");

            return;
        }

        $this->pages = $this->getTableGateway(PageTableGateway::class)->select(['sitemapId' => $this->sitemap->getId()]);
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        /** @var PageTypeInterface $pageTypeObject */
        $pageTypeObject = $this->getServiceManager()->get(PageTypeProvider::class)->get($this->pageType);

        $this->sitemap->setPageType($this->pageType)
            ->setExclude($pageTypeObject->getExclude())
            ->setHandle($pageTypeObject->getHandle())
            ->setTerminal($pageTypeObject->getTerminal());

        $this->getTableGateway(SitemapTableGateway::class)->update($this->sitemap);

        /** @var Page $page */
        foreach ($this->pages as $page) {
            $this->getTableGateway(PageVersionTableGateway::class)->delete(['pageId' => $page->getId()]);

            $page->setStatus(Page::STATUS_OFFLINE);
            $pageContent = [
                'status' => Page::STATUS_OFFLINE,
                'name'   => $page->getName()
            ];

            $pageContentObject = $pageTypeObject->getPageContent();
            $pageContentObject->setContent($pageContent);

            $this
                ->getServiceManager()
                ->get('Frontend42\Page\EventManager')
                ->trigger(
                    PageEvent::EVENT_ADD_PRE,
                    $page,
                    ['sitemap' => $this->sitemap, 'approved' => true, 'pageContent' => $pageContentObject]
                );

            $this->getTableGateway(PageTableGateway::class)->update($page);

            $pageVersion = new PageVersion();
            $pageVersion->setPageId($page->getId())
                ->setVersionId(1)
                ->setCreated(new \DateTime())
                ->setContent(Json::encode($pageContentObject->getContent()))
                ->setCreatedBy($this->createdBy);

            $this->getTableGateway(PageVersionTableGateway::class)->insert($pageVersion);

            $this
                ->getServiceManager()
                ->get('Frontend42\Page\EventManager')
                ->trigger(
                    PageEvent::EVENT_ADD_POST,
                    $page,
                    ['sitemap' => $this->sitemap, 'approved' => true, 'pageContent' => $pageContentObject]
                );
        }
    }
}
