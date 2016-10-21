<?php
namespace Frontend42\Command\Sitemap;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Core42\I18n\Localization\Localization;
use Frontend42\Command\Page\AddPageCommand;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\Selector\AvailablePageTypesSelector;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Predicate\Expression;

class AddSitemapCommand extends AbstractCommand
{
    /**
     * @var null|int
     */
    protected $parentId;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $pageType;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Sitemap
     */
    protected $parent;

    /**
     * @param int|null $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

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
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

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


    public function hydrate(array $values)
    {
        $this->setPageType($values['pageType']);
        $this->setName($values['name']);
    }

    protected function preExecute()
    {
        if (empty($this->pageType)) {
            $this->addError("pageType", "invalid pageType");

            return;
        }

        try {
            $this->getServiceManager()->get(PageTypePluginManager::class)->get($this->pageType);
        } catch (\Exception $e) {
            $this->addError("pageType", "invalid pageType");

            return;
        }

        /** @var Localization $localization */
        $localization = $this->getServiceManager()->get(Localization::class);
        if (!in_array($this->locale, $localization->getAvailableLocales())) {
            $this->addError("locale", "invalid locale");

            return;
        }

        if ($this->parentId > 0) {
            $this->parentId = (int) $this->parentId;
            $this->parent = $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary($this->parentId);

            if (empty($this->parent)) {
                $this->addError("parentId", "invalid parentId");

                return;
            }
        } else {
            $this->parentId = null;
        }

        /** @var AvailablePageTypesSelector $availablePageTypeSelector */
        $availablePageTypeSelector = $this->getSelector(AvailablePageTypesSelector::class);
        $availablePageTypes = $availablePageTypeSelector
            ->setParentId($this->parentId)
            ->getResult();

        if (!isset($availablePageTypes[$this->pageType])) {
            $this->addError("pageType", "invalid pageType");

            return;
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $select = $this->getTableGateway(SitemapTableGateway::class)
            ->getSql()
            ->select();

        $select->where(['parentId' => (empty($this->parentPageId)) ? null : $this->parent->getParentId()]);
        $select->columns(['orderNr' => new Expression('MAX(orderNr)')]);
        $statement = $this
            ->getTableGateway(SitemapTableGateway::class)
            ->getSql()
            ->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();
        $orderNr = $result['orderNr'];
        if (empty($orderNr)) {
            $orderNr = 0;
        }
        $orderNr++;

        $handle = $this->getServiceManager()->get(PageTypePluginManager::class)->get($this->pageType)->getHandle();

        $sitemap = new Sitemap();
        $sitemap->setParentId($this->parentId)
            ->setOrderNr($orderNr)
            ->setPageType($this->pageType)
            ->setHandle($handle);

        $this->getTableGateway(SitemapTableGateway::class)->insert($sitemap);

        foreach ($this->getServiceManager()->get(Localization::class)->getAvailableLocales() as $locale) {
            /** @var AddPageCommand $cmd */
            $cmd = $this->getCommand(AddPageCommand::class);
            $cmd->setUser($this->user)
                ->setLocale($locale)
                ->setSitemap($sitemap);

            if ($this->locale === $locale) {
                $cmd->setName($this->name);
            }

            $cmd->run();
        }
    }
}
