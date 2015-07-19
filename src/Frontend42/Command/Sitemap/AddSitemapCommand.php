<?php
namespace Frontend42\Command\Sitemap;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Frontend42\Model\Page;
use Frontend42\Model\PageVersion;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeContent;
use Frontend42\PageType\PageTypeInterface;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Json\Json;

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

    protected function preExecute()
    {
        if (!empty($this->parentPageId)) {
            $this->parentPage = $this->getTableGateway('Frontend42\Page')->selectByPrimary((int) $this->parentPageId);
        }

        if ($this->createdUser !== null) {
            $this->createdBy = $this->createdUser->getId();
        }

        $select = $this->getTableGateway('Frontend42\Sitemap')
            ->getSql()
            ->select();

        $select->where(['parentId' => (empty($this->parentPageId)) ? null : $this->parentPage->getSitemapId()]);
        $select->columns(['orderNr' => new Expression('MAX(orderNr)')]);
        $statement = $this->getTableGateway('Frontend42\Sitemap')->getSql()->prepareStatementForSqlObject($select);
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
        $sitemap = new Sitemap();
        $sitemap->setPageType($this->pageType)
            ->setOrderNr($this->orderNr)
            ->setCreated(new \DateTime())
            ->setCreatedBy($this->createdBy)
            ->setUpdated(new \DateTime())
            ->setUpdatedBy($this->createdBy);

        if (!empty($this->parentPage)) {
            $sitemap->setParentId($this->parentPage->getSitemapId());
        }

        /** @var PageTypeInterface $pageTypeObject */
        $pageTypeObject = $this->getServiceManager()->get('Frontend42\PageTypeProvider')->getPageType($this->pageType);

        $pageTypeObject->prepareForAdd($sitemap);

        $this->getTableGateway('Frontend42\Sitemap')->insert($sitemap);

        $defaultLocale = $this->getServiceManager()->get('Localization')->getDefaultLocale();
        if (!empty($this->parentPage)) {
            $defaultLocale = $this->parentPage->getLocale();
        }

        foreach ($this->getServiceManager()->get('Localization')->getAvailableLocales() as $locale) {
            $page = new Page();
            $page->setLocale($locale)
                ->setStatus(Page::STATUS_OFFLINE)
                ->setSitemapId($sitemap->getId())
                ->setCreated(new \DateTime())
                ->setCreatedBy($this->createdBy)
                ->setUpdated(new \DateTime())
                ->setUpdatedBy($this->createdBy);

            $pageContent = [
                'status' => Page::STATUS_OFFLINE
            ];

            if ($locale === $defaultLocale) {
                $page->setName($this->name);

                $pageContent['name'] = $this->name;
            }

            $pageTypeContent = new PageTypeContent();
            $pageTypeContent->setContent($pageContent);

            $pageTypeObject->savePage($pageTypeContent, $page, true);

            $this->getTableGateway('Frontend42\Page')->insert($page);

            $pageVersion = new PageVersion();
            $pageVersion->setPageId($page->getId())
                ->setVersionId(1)
                ->setContent(Json::encode($pageTypeContent->getContent()))
                ->setApproved(new \DateTime())
                ->setCreated(new \DateTime())
                ->setCreatedBy($this->createdBy);

            $this->getTableGateway('Frontend42\PageVersion')->insert($pageVersion);
        }

        return $this->getTableGateway('Frontend42\Page')->select([
            'sitemapId' => $sitemap->getId(),
            'locale'    => $defaultLocale
        ])->current();
    }
}
