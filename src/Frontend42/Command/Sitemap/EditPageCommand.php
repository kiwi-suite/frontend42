<?php
namespace Frontend42\Command\Sitemap;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Frontend42\Event\SitemapEvent;
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
    protected $updatedUser;

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
     * @var bool
     */
    protected $approve = false;

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
     * @param User $updatedUser
     * @return $this
     */
    public function setUpdatedUser(User $updatedUser)
    {
        $this->updatedUser = $updatedUser;

        return $this;
    }

    /**
     * @param boolean $approve
     * @return $this
     */
    public function setApprove($approve)
    {
        $this->approve = $approve;

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
        $this->pageTypeContent->setFromFormData($this->content);
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $this->pageVersion->setContent(Json::encode($this->pageTypeContent->getContent()));

        if (!$this->pageVersion->hasChanged("content")) {
            return $this->pageVersion;
        }

        $this
            ->getServiceManager()
            ->get('Frontend42\Sitemap\EventManager')
            ->trigger(SitemapEvent::EVENT_EDIT_PRE, $this->page, [
                    'pageType' => $this->pageType,
                    'sitemap' => $this->sitemap]
            );

        $pageVersionTableGateway = $this->getTableGateway('Frontend42\PageVersion');

        $pageVersion = new PageVersion();
        $pageVersion->setVersionId($this->pageVersion->getVersionId() + 1)
            ->setContent($this->pageVersion->getContent())
            ->setPageId($this->page->getId())
            ->setCreated(new \DateTime())
            ->setCreatedBy($this->updatedUser->getId());

        if ($this->approve) {
            $pageVersionTableGateway->update(['approved' => null], ['pageId' => $this->page->getId()]);
            $pageVersion->setApproved(new \DateTime());

            $this
                ->getServiceManager()
                ->get('Frontend42\Sitemap\EventManager')
                ->trigger(SitemapEvent::EVENT_APPROVED, $this->page, [
                        'pageType' => $this->pageType,
                        'sitemap' => $this->sitemap]
                );
        }

        $this->getTableGateway('Frontend42\PageVersion')->insert($pageVersion);

        $this->pageType->savePage($this->pageTypeContent, $this->page, $this->approve);

        $this->page->setUpdated(new \DateTime())
                   ->setUpdatedBy($this->updatedUser->getId());

        $this->getTableGateway('Frontend42\Page')->update($this->page);

        $this->getCommand('Frontend42\Router\CreateRouteConfig')->run();

        $this
            ->getServiceManager()
            ->get('Frontend42\Sitemap\EventManager')
            ->trigger(SitemapEvent::EVENT_EDIT_POST, $this->page, [
                    'pageType' => $this->pageType,
                    'sitemap' => $this->sitemap]
            );

        $cacheKey = 'page_' . $this->page->getId() . '_' . PageVersionSelector::VERSION_APPROVED;
        $this->getServiceManager()->get('Cache\Sitemap')->removeItem($cacheKey);

        $result = $this->getTableGateway('Frontend42\BlockInheritance')->select([
            'targetPageId' => $this->page->getId()
        ]);
        foreach ($result as $_res) {
            $this
                ->getServiceManager()
                ->get('Cache\Block')
                ->removeItem('block_inheritance_' . $_res->getSourcePageId() . '_' . $_res->getSection());
        }

        return $pageVersion;
    }
}
