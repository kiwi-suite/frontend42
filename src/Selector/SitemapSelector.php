<?php
namespace Frontend42\Selector;

use Core42\Db\ResultSet\ResultSet;
use Core42\Selector\AbstractDatabaseSelector;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Select;

class SitemapSelector extends AbstractDatabaseSelector
{
    /**
     * @var string
     */
    protected $locale;

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
        $flatSitemap = $this->getFlatTree();

        $tree = [];
        foreach ($flatSitemap as &$item) {
            /** @var Sitemap $sitemap */
            $sitemap = $item['sitemap'];
            if ($sitemap->getParentId() > 0) {
                $parent =& $flatSitemap[$sitemap->getParentId()];
                $parent['children'][] =& $item;

                continue;
            }

            $tree[] =& $item;
        }

        return $tree;
    }

    /**
     * @param array $sitemap
     * @return array
     */
    protected function getTreePart(array $sitemap)
    {
        /** @var AuthorizationService $permission */
        $permission = $this->getServiceManager()->get('Permission')->getService('admin42');

        $return = [];
        foreach ($sitemap as $item) {
            if ($permission->isGranted('sitemap/manage/' . $item['sitemap']->getId())) {
                $return[] = $item;

                continue;
            }

            if (!empty($item['children'])) {
                $return = array_merge($this->getTreePart($item['children']), $return);
            }
        }

        return $return;
    }

    /**
     * @return Select|string|ResultSet
     */
    protected function getSelect()
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

        $select->where(['p.locale' => $this->locale]);

        $select->order($sitemapTableName.'.orderNr ASC');

        return $select;

        /*
        $sql = new Sql($this->getServiceManager()->get('Db\Master'));
        $select = $sql->select();

        $select->from([
            's' => $this->getTableGateway(SitemapTableGateway::class)->getTable()
        ]);
        $select->join([
            'p' => $this->getTableGateway(PageTableGateway::class)->getTable()
        ], "s.id=p.sitemapId");

        $select->where(['p.locale' => $this->locale]);

        if ($this->includeExclude === false) {
            $select->where(['s.exclude' => 'false']);
        }

        if ($this->includeOffline === false) {
            $select->where(['p.status' => 'online']);
        }

        $select->order('s.orderNr ASC');

        return $select;*/
    }

    /**
     * @return array
     */
    protected function getFlatTree()
    {
        $flat = [];
        $sql = $this->getTableGateway(SitemapTableGateway::class)->getSql();
        $statement = $sql->prepareStatementForSqlObject($this->getSelect());
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

            $flat[$sitemap->getId()] = [
                'page' => $page,
                'sitemap' => $sitemap,
                'children' => []
            ];
        }

        return $flat;
    }
}
