<?php
namespace Frontend42\Command\Sitemap;

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
     * @var int|null
     */
    protected $parentId;

    /**
     * @var string
     */
    protected $pageType;

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
        $this->parentPage = $this->getTableGateway('Frontend42\Page')->selectByPrimary((int) $this->parentPageId);

        $select = $this->getTableGateway('Frontend42\Sitemap')
            ->getSql()
            ->select();

        $select->where(['parentId' => $this->parentPage->getId()]);
        $select->columns(['orderNr' => new Expression('MAX(orderNr)')]);
        $statement = $this->getTableGateway('Frontend42\Sitemap')->getSql()->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();

        $this->orderNr = $result['orderNr'];
        if (empty($this->orderNr)) {
            $this->orderNr = 1;
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $sitemap = new Sitemap();
        $sitemap->setParentId($this->parentId)
            ->setPageType($this->pageType)
            ->setOrderNr($this->orderNr);

        /** @var PageTypeInterface $pageTypeObject */
        $pageTypeObject = $this->getServiceManager()->get('Frontend42\PageTypeProvider')->getPageType($this->pageType);

        $pageTypeObject->prepareForAdd($sitemap);

        $this->getTableGateway('Frontend42\Sitemap')->insert($sitemap);

        foreach ($this->getServiceManager()->get('Localization')->getAvailableLocales() as $locale) {
            $page = new Page();
            $page->setLocale($locale)
                ->setStatus(Page::STATUS_OFFLINE)
                ->setSitemapId($sitemap->getId());

            $pageContent = [
                'status' => Page::STATUS_OFFLINE
            ];

            if ($locale === $this->parentPage->getLocale()) {
                $page->setName($this->name);

                $pageContent['name'] = $this->name;
            }

            $pageTypeContent = new PageTypeContent();
            $pageTypeContent->setContent($pageContent);

            $pageTypeObject->savePage($pageTypeContent, $page);

            $this->getTableGateway('Frontend42\Page')->insert($page);

            $pageVersion = new PageVersion();
            $pageVersion->setPageId($page->getId())
                ->setVersionId(1)
                ->setCreated(new \DateTime())
                ->setContent(Json::encode($pageTypeContent->getContent()))
                ->setCreatedBy($this->createdBy);

            $this->getTableGateway('Frontend42\PageVersion')->insert($pageVersion);
        }

        return $this->getTableGateway('Frontend42\Page')->select([
            'sitemapId' => $sitemap->getId(),
            'locale'    => $this->parentPage->getLocale()
        ])->current();
    }
}
