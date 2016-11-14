<?php
namespace Frontend42\Selector;

use Core42\Selector\AbstractSelector;
use Core42\Selector\CacheAbleTrait;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\TableGateway\NavigationTableGateway;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class NavigationSelector extends AbstractSelector
{
    use CacheAbleTrait;

    /**
     * @var string
     */
    protected $navigation;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param string $navigation
     * @return $this
     */
    public function setNavigation($navigation)
    {
        $this->navigation = $navigation;

        return $this;
    }

    /**
     * @param $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    protected function getCacheName()
    {
        return 'navigation';
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return $this->navigation . $this->locale;
    }

    /**
     * @return array
     */
    protected function getUncachedResult()
    {
        return $this->getTree();
    }

    /**
     * @return array
     */
    protected function getTree()
    {
        $sitemapTableName = $this->getTableGateway(SitemapTableGateway::class)->getTable();
        $sql = $this->getTableGateway(SitemapTableGateway::class)->getSql();
        $select = $sql->select();
        $select->columns(['parentId', 'nestedLeft', 'nestedRight', 'level']);

        $select->join(
            ['p' => $this->getTableGateway(PageTableGateway::class)->getTable()],
            "{$sitemapTableName}.id=p.sitemapId",
            ['pageId' => 'id', 'sitemapId']
        );

        $select->join(
            ['nav' => $this->getTableGateway(NavigationTableGateway::class)->getTable()],
            new Expression("p.id=nav.pageId AND nav.nav='{$this->navigation}'"),
            []
        );

        $select->where(function (Where $where) use ($sitemapTableName){
            $where->isNotNull($sitemapTableName .".nestedLeft");
        });

        $select->where(['p.locale' => $this->locale]);
        $select->order($sitemapTableName.'.nestedLeft ASC');

        $tree = [];
        $hash = [];
        $firstLevel = false;

        $sql = $this->getTableGateway(SitemapTableGateway::class)->getSql();
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        foreach ($result as $res) {
            $hash[$res['sitemapId']] = [
                'pageId' => $res['pageId'],
                'children' => [],
            ];
            if ($firstLevel === false) {
                $firstLevel = $res['level'];
            }

            if ($res['level'] !== $firstLevel && !isset($hash[$res['parentId']])) {
                continue;
            }

            if ($res['level'] === $firstLevel) {
                $tree[] =& $hash[$res['sitemapId']];

                continue;
            }

            $hash[$res['parentId']]['children'][] =& $hash[$res['sitemapId']];
        }

        return $tree;
    }
}
