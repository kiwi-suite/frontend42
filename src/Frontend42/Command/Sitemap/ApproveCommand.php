<?php
namespace Frontend42\Command\Sitemap;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Frontend42\Event\SitemapEvent;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeContent;
use Zend\Json\Json;

class ApproveCommand extends AbstractCommand
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
     * @var User
     */
    protected $updatedUser;

    /**
     * @var int
     */
    protected $pageVersionId;

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
     * @param int $pageVersionId
     * @return $this
     */
    public function setPageVersionId($pageVersionId)
    {
        $this->pageVersionId = $pageVersionId;

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
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $pageVersionTableGateway = $this->getTableGateway('Frontend42\PageVersion');

        $result = $pageVersionTableGateway->select([
            'pageId' => $this->page->getId(),
            'versionId' => $this->pageVersionId
        ]);

        if ($result->count() <> 1) {
            return;
        }

        $pageVersionTableGateway->update(['approved' => null], ['pageId' => $this->page->getId()]);

        $pageVersion = $result->current();
        $pageVersion->setApproved(new \DateTime());

        $pageVersionTableGateway->update($pageVersion);

        $this->page->setUpdated(new \DateTime())
                   ->setUpdatedBy($this->updatedUser->getId());

        $pageTypeContent = new PageTypeContent();
        $pageTypeContent->setContent(Json::decode($pageVersion->getContent(), Json::TYPE_ARRAY));

        $pageTypeObject = $this->getServiceManager()
            ->get('Frontend42\PageTypeProvider')
            ->getPageType($this->sitemap->getPageType());
        $pageTypeObject->savePage($pageTypeContent, $this->page, true);


        $this->getTableGateway('Frontend42\Page')->update($this->page);

        $this->getCommand('Frontend42\Router\CreateRouteConfig')->run();

        $this
            ->getServiceManager()
            ->get('Frontend42\Sitemap\EventManager')
            ->trigger(SitemapEvent::EVENT_APPROVED, $this->page, [
                'pageType' => $pageTypeObject,
                'sitemap' => $this->sitemap]
            );

        return $pageVersion;
    }
}
