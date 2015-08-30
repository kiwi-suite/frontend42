<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Core42\Db\ResultSet\ResultSet;
use Frontend42\Event\SitemapEvent;
use Frontend42\Model\Page;
use Frontend42\Model\PageVersion;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeContent;
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

    protected function preExecute()
    {
        if ($this->sitemapId > 0) {
            $this->sitemap = $this->getTableGateway('Frontend42\Sitemap')->selectByPrimary((int) $this->sitemapId);
        }

        if (empty($this->sitemap)) {
            $this->addError("sitemap", "invalid sitemap");

            return;
        }

        $this->pages = $this->getTableGateway('Frontend42\Page')->select(['sitemapId' => $this->sitemap->getId()]);
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $this->sitemap->setPageType($this->pageType)
            ->setExclude(false)
            ->setHandle(null)
            ->setTerminal(false);

        $pageTypeObject = $this->getServiceManager()->get('Frontend42\PageTypeProvider')->getPageType($this->pageType);

        $pageTypeObject->prepareForAdd($this->sitemap);

        $this->getTableGateway('Frontend42\Sitemap')->update($this->sitemap);

        /** @var Page $page */
        foreach ($this->pages as $page) {
            $this->getTableGateway('Frontend42\PageVersion')->delete(['pageId' => $page->getId()]);

            $page->setStatus(Page::STATUS_OFFLINE);
            $pageContent = [
                'status' => Page::STATUS_OFFLINE,
                'name'   => $page->getName()
            ];

            $pageTypeContent = new PageTypeContent();
            $pageTypeContent->setContent($pageContent);

            $pageTypeObject->savePage($pageTypeContent, $page, true);

            $this->getTableGateway('Frontend42\Page')->update($page);

            $pageVersion = new PageVersion();
            $pageVersion->setPageId($page->getId())
                ->setVersionId(1)
                ->setCreated(new \DateTime())
                ->setContent(Json::encode($pageTypeContent->getContent()))
                ->setCreatedBy($this->createdBy);

            $this->getTableGateway('Frontend42\PageVersion')->insert($pageVersion);

            $this
                ->getServiceManager()
                ->get('Frontend42\Sitemap\EventManager')
                ->trigger(SitemapEvent::EVENT_CHANGE_PAGETYPE, $page, [
                        'pageType' => $pageTypeObject,
                        'sitemap' => $this->sitemap]
                );

        }
    }
}
