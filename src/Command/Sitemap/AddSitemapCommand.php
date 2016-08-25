<?php
namespace Frontend42\Command\Sitemap;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Core42\I18n\Localization\Localization;
use Frontend42\Command\PageVersion\ApproveCommand;
use Frontend42\Command\PageVersion\CreateCommand;
use Frontend42\Event\PageEvent;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Provider\PageTypeProvider;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Predicate\Expression;

class AddSitemapCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $pageType;

    /**
     * @var User
     */
    protected $createdUser;

    /**
     * @var int
     */
    protected $parentPageId;

    /**
     * @var Page
     */
    protected $parentPage;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $orderNr;

    /**
     * @var int
     */
    protected $createdBy;

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
     * @param int $parentPageId
     * @return $this
     */
    public function setParentPageId($parentPageId)
    {
        $this->parentPageId = $parentPageId;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @param int $createdBy
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
        if (!empty($this->parentPageId)) {
            $this->parentPage = $this
                ->getTableGateway(PageTableGateway::class)
                ->selectByPrimary((int) $this->parentPageId);
        }

        if ($this->createdUser !== null) {
            $this->createdBy = $this->createdUser->getId();
        }

        $select = $this->getTableGateway(SitemapTableGateway::class)
            ->getSql()
            ->select();

        $select->where(['parentId' => (empty($this->parentPageId)) ? null : $this->parentPage->getSitemapId()]);
        $select->columns(['orderNr' => new Expression('MAX(orderNr)')]);
        $statement = $this
            ->getTableGateway(SitemapTableGateway::class)
            ->getSql()
            ->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();

        $this->orderNr = $result['orderNr'];
        if (empty($this->orderNr)) {
            $this->orderNr = 0;
        }
        $this->orderNr++;
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        /** @var PageTypeInterface $pageTypeObject */
        $pageTypeObject = $this->getServiceManager()->get(PageTypeProvider::class)->get($this->pageType);

        $sitemap = new Sitemap();
        $sitemap->setPageType($this->pageType)
            ->setOrderNr($this->orderNr)
            ->setExclude($pageTypeObject->getExclude())
            ->setHandle($pageTypeObject->getHandle())
            ->setTerminal($pageTypeObject->getTerminal())
            ->setCreated(new \DateTime())
            ->setCreatedBy($this->createdBy)
            ->setUpdated(new \DateTime())
            ->setUpdatedBy($this->createdBy);

        if (!empty($this->parentPage)) {
            $sitemap->setParentId($this->parentPage->getSitemapId());
        }

        $this->getTableGateway(SitemapTableGateway::class)->insert($sitemap);

        $defaultLocale = $this->getServiceManager()->get(Localization::class)->getDefaultLocale();
        if (!empty($this->parentPage)) {
            $defaultLocale = $this->parentPage->getLocale();
        }

        foreach ($this->getServiceManager()->get(Localization::class)->getAvailableLocales() as $locale) {
            $page = new Page();
            $page->setLocale($locale)
                ->setStatus(Page::STATUS_OFFLINE)
                ->setSitemapId($sitemap->getId())
                ->setCreated(new \DateTime())
                ->setCreatedBy($this->createdBy)
                ->setUpdated(new \DateTime())
                ->setUpdatedBy($this->createdBy);

            $pageContent = [
                'status' => $page->getStatus()
            ];

            if ($locale === $defaultLocale) {
                $pageContent['name'] = $this->name;
            }

            $pageContentObject = $pageTypeObject->getPageContent();
            $pageContentObject->setContent($pageContent);

            $this
                ->getServiceManager()
                ->get('Frontend42\Page\EventManager')
                ->trigger(
                    PageEvent::EVENT_ADD_PRE,
                    $page,
                    ['sitemap' => $sitemap, 'approved' => true, 'pageContent' => $pageContentObject]
                );

            $this->getTableGateway(PageTableGateway::class)->insert($page);

            $pageVersion = $this->getCommand(CreateCommand::class)
                ->setPageId($page->getId())
                ->setContent($pageContentObject->getContent())
                ->setCreatedBy($this->createdUser)
                ->run();

            $this->getCommand(ApproveCommand::class)
                ->setVersion($pageVersion)
                ->run();

            $this
                ->getServiceManager()
                ->get('Frontend42\Page\EventManager')
                ->trigger(
                    PageEvent::EVENT_ADD_POST,
                    $page,
                    ['sitemap' => $sitemap, 'approved' => true, 'pageContent' => $pageContentObject]
                );
        }

        $model = $this->getTableGateway(PageTableGateway::class)->select([
            'sitemapId' => $sitemap->getId(),
            'locale'    => $defaultLocale
        ])->current();

        return $model;
    }
}
