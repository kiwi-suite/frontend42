<?php
namespace Frontend42\Selector;

use Core42\Selector\AbstractSelector;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Where;

class AngularSitemapSelector extends AbstractSelector
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var AvailablePageTypesSelector
     */
    protected $pageTypeSelector;

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
     * @return mixed
     */
    public function getResult()
    {
        $this->pageTypeSelector = $this->getSelector(AvailablePageTypesSelector::class);

        return $this->prepareJsonTree($this->getTree());
    }

    /**
     * @param $items
     * @return array
     */
    protected function prepareJsonTree($items)
    {
        $tree = [];
        foreach ($items as $_item) {
            if (empty($_item['page'])) {
                continue;
            }
            $alternateNames = [];
            $title = $_item['page']->getName();
            if (empty($title)) {
                foreach ($_item['allPages'] as $alternatePage) {
                    $alternateNames[] = [
                        'region' => strtolower(\Locale::getRegion($alternatePage->getLocale())),
                        'name' => $alternatePage->getName(),
                    ];
                }
            }

            $pageType = $this
                ->getServiceManager()
                ->get(PageTypePluginManager::class)
                ->get($_item['sitemap']->getPageType());

            $availablePageTypes = $this
                ->pageTypeSelector
                ->setParent($_item['sitemap'])
                ->getResult();

            $node = [
                'id'        => $_item['sitemap']->getId(),
                'pageType'  => $_item['sitemap']->getPageType(),
                'pageId'    => $_item['page']->getId(),
                'locale'    => $_item['page']->getLocale(),
                'title'     => $title,
                'status'    => $_item['page']->getStatus(),
                'isTerminal'=> $pageType->isTerminal(),
                'isSorting' => $pageType->isSorting(),
                'pageTypes' => $availablePageTypes,
                'alternateNames' => $alternateNames,
                'items'     => [],
            ];
            if (!empty($_item['children']) && !$pageType->isTerminal()) {
                $node['items'] = $this->prepareJsonTree($_item['children']);
            }

            $tree[] = $node;
        }

        return $tree;
    }

    protected function getTree()
    {
        $flat = $this->getFlatSitemap();

        $tree = [];
        foreach ($flat as &$item) {
            /** @var Sitemap $sitemap */
            $sitemap = $item['sitemap'];
            if ($sitemap->getParentId() > 0) {
                $parent =& $flat[$sitemap->getParentId()];
                $parent['children'][] =& $item;

                continue;
            }

            $tree[] =& $item;
        }

        return $tree;
    }

    protected function getFlatSitemap()
    {
        $flat = [];
        $sql = $this->getTableGateway(SitemapTableGateway::class)->getSql();
        $statement = $sql->prepareStatementForSqlObject($this->getFlatSelect());
        $result = $statement->execute();
        foreach ($result as $res) {
            $sitemap = $this->getTableGateway(SitemapTableGateway::class)->getHydrator()->hydrate($res, new Sitemap());
            $pageColumns = [];
            foreach ($res as $key => $value) {
                if (substr($key, 0, 12) != "prefix_page_") {
                    continue;
                }
                $pageColumns[substr($key, 12)] = $value;
            }
            $page = $this->getTableGateway(PageTableGateway::class)->getHydrator()->hydrate($pageColumns, new Page());

            if (!isset($flat[$sitemap->getId()])) {
                $flat[$sitemap->getId()] = [
                    'sitemap' => $sitemap,
                    'page' => null,
                    'allPages' => [],
                    'children' => [],
                ];
            }



            if ($page->getLocale() == $this->locale) {
                $flat[$sitemap->getId()]['page'] = $page;

                continue;
            }
            $flat[$sitemap->getId()]['allPages'][] = $page;
        }

        return $flat;
    }

    protected function getFlatSelect()
    {
        $sitemapTableName = $this->getTableGateway(SitemapTableGateway::class)->getTable();
        $sql = $this->getTableGateway(SitemapTableGateway::class)->getSql();
        $select = $sql->select();

        $pageColumns = $this->getTableGateway(PageTableGateway::class)->getColumns();
        $pageAliasColumns = [];
        foreach ($pageColumns as $column) {
            $pageAliasColumns['prefix_page_'.$column] = $column;
        }

        $select->join(
            ['p' => $this->getTableGateway(PageTableGateway::class)->getTable()],
            "{$sitemapTableName}.id=p.sitemapId",
            $pageAliasColumns
        );

        $select->where(function (Where $where) use ($sitemapTableName){
           $where->isNotNull($sitemapTableName .".nestedLeft");
        });

        $select->order($sitemapTableName.'.nestedLeft ASC');
        return $select;
    }
}
