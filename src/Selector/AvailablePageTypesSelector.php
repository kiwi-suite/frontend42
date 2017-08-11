<?php
namespace Frontend42\Selector;

use Core42\Selector\AbstractSelector;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class AvailablePageTypesSelector extends AbstractSelector
{
    /**
     * @var null|int
     */
    protected $parentId;

    /**
     * @var array
     */
    protected $handleCache;

    /**
     * @var array
     */
    protected $parentCache;

    /**
     * @var Sitemap
     */
    protected $parent;

    /**
     * @var bool
     */
    protected $checkHandle = true;

    /**
     * @param $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @param Sitemap $parent
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @param bool $checkHandle
     * @return $this
     */
    public function setCheckHandle($checkHandle)
    {
        $this->checkHandle = $checkHandle;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        /** @var PageTypePluginManager $pageTypePluginManager */
        $pageTypePluginManager = $this->getServiceManager()->get(PageTypePluginManager::class);
        $pageTypes = [];

        $parentPageType = null;
        if ($this->parentId !== null) {
            $this->parent = $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary($this->parentId);
        }
        if (!empty($this->parent)) {
            $parentPageType = $pageTypePluginManager->get($this->parent->getPageType());
        }

        $availablePageTypes = $pageTypePluginManager->getAvailablePageTypes();
        if ($parentPageType instanceof PageTypeInterface && is_array($parentPageType->getAllowedChildren())) {
            $availablePageTypes = $parentPageType->getAllowedChildren();
        }

        foreach ($availablePageTypes as $pageTypeName) {
            /** @var PageTypeInterface $pageType */
            $pageType = $pageTypePluginManager->get($pageTypeName);

            if ($this->checkHandle && !$this->checkHandle($pageType)) {
                continue;
            }

            if ($parentPageType === null) {
                //PageTypes which needs specific parents can't be at the root element
                if (is_array($pageType->getAllowedParents())) {
                    continue;
                }

                if ($pageType->getRoot() === null || $pageType->getRoot() === true) {
                    $pageTypes[$pageType->getName()] = $pageType->getLabel();
                }

                continue;
            }

            if ($pageType->getRoot() === true) {
                continue;
            }

            if (is_array($pageType->getAllowedParents())
                && !in_array($parentPageType->getName(), $pageType->getAllowedParents())
            ) {
                continue;
            }

            $pageTypes[$pageType->getName()] = $pageType->getLabel();
        }

        return $pageTypes;
    }

    /**
     * @param PageTypeInterface $pageType
     * @return bool
     */
    protected function checkHandle(PageTypeInterface $pageType)
    {
        if ($pageType->getHandle() === null) {
            return true;
        }

        if ($this->handleCache === null) {
            $result = $this->getTableGateway(SitemapTableGateway::class)->select(function (Select $select) {
                $select->where(function (Where $where) {
                    $where->isNotNull('handle');
                });
            });

            $this->handleCache = [];

            /** @var Sitemap $sitemap */
            foreach ($result as $sitemap) {
                $this->handleCache[] = $sitemap->getHandle();
            }
        }

        return !in_array($pageType->getHandle(), $this->handleCache);
    }
}
