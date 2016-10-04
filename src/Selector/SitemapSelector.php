<?php
namespace Frontend42\Selector;

use Core42\Db\ResultSet\ResultSet;
use Core42\Selector\AbstractDatabaseSelector;
use Frontend42\Model\Page;
use Frontend42\Model\PageVersion;
use Frontend42\Model\Sitemap;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\PageVersionTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class SitemapSelector extends AbstractDatabaseSelector
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var boolean
     */
    protected $enablePageVersion = false;

    /**
     * @var bool
     */
    protected $enableStatusCheck = true;

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
     * @param boolean $enablePageVersion
     * @return $this
     */
    public function setEnablePageVersion($enablePageVersion)
    {
        $this->enablePageVersion = $enablePageVersion;

        return $this;
    }

    /**
     * @param boolean $enableStatusCheck
     * @return $this
     */
    public function setEnableStatusCheck($enableStatusCheck)
    {
        $this->enableStatusCheck = $enableStatusCheck;

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

        if ($this->enablePageVersion) {
            $pageVersionColumns = $this->getTableGateway(PageVersionTableGateway::class)->getColumns();
            $pageVersionAliasColumns = [];
            foreach ($pageVersionColumns as $column) {
                $pageVersionAliasColumns['prefix_version_'.$column] = $column;
            }

            $select->join(
                ['v' => $this->getTableGateway(PageVersionTableGateway::class)->getTable()],
                "p.id = v.pageId",
                $pageVersionAliasColumns
            );

            $select->where(function (Where $where) {
                $where->isNotNull('v.approved');
            });
        }

        $select->where(['p.locale' => $this->locale]);

        if ($this->enableStatusCheck) {
            $select->where(['p.status' => 'online']);
        }

        $select->order($sitemapTableName.'.orderNr ASC');
        //var_dump($select->getSqlString($this->getServiceManager()->get('Db\Master')->getPlatform()));
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

            if ($this->enablePageVersion) {
                $pageVersionColumns = [];
                foreach ($res as $key => $value) {
                    if (substr($key, 0, 15) != "prefix_version_") {
                        continue;
                    }
                    $pageVersionColumns[substr($key, 15)] = $value;
                }
                $flat[$sitemap->getId()]['pageVersion'] = $this
                    ->getTableGateway(PageVersionTableGateway::class)
                    ->getHydrator()
                    ->hydrate($pageVersionColumns, new PageVersion());
            }
        }

        return $flat;
    }
}
