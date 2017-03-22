<?php
namespace Frontend42\Selector;

use Core42\Selector\AbstractSelector;
use Core42\Selector\CacheAbleTrait;
use Frontend42\Model\Page;
use Frontend42\Router\PageRoute;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Thepixeldeveloper\Sitemap\Url;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Where;

class SitemapXmlSelector extends AbstractSelector
{
    /**
     * @return mixed
     */
    public function getResult()
    {
        $sitemapTableName = $this->getTableGateway(SitemapTableGateway::class)->getTable();
        $sql = $this->getTableGateway(SitemapTableGateway::class)->getSql();
        $select = $sql->select();
        $select->columns([]);

        $select->join(
            ['p' => $this->getTableGateway(PageTableGateway::class)->getTable()],
            "{$sitemapTableName}.id=p.sitemapId",
            ['pageId' => 'id']
        );

        $select->where([
            'p.status' => Page::STATUS_ONLINE,
        ]);

        $select->where(function(Where $where){
            $publishedFrom1 = new Where();
            $publishedFrom1->isNull('p.publishedFrom');
            $publishedFrom2 = new Where();
            $publishedFrom2->lessThanOrEqualTo('p.publishedFrom', date('Y-m-d H:i:s'));

            $publishedFrom = new PredicateSet([$publishedFrom1, $publishedFrom2], PredicateSet::COMBINED_BY_OR);
            $where->addPredicate($publishedFrom);

            $publishedUntil1 = new Where();
            $publishedUntil1->isNull('p.publishedUntil');
            $publishedUntil2 = new Where();
            $publishedUntil2->greaterThanOrEqualTo('p.publishedUntil', date('Y-m-d H:i:s'));

            $publishedUntil = new PredicateSet([$publishedUntil1, $publishedUntil2], PredicateSet::COMBINED_BY_OR);
            $where->addPredicate($publishedUntil);
        });

        $sql = $this->getTableGateway(SitemapTableGateway::class)->getSql();
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $baseRoute = $this->getServiceManager()->get('config')['project']['project_base_url'];

        $return = [];
        foreach ($result as $res) {
            $loc = $baseRoute . $this
                ->getServiceManager()
                ->get(PageRoute::class)
                ->assemble($res['pageId']);

            $return[] = new Url($loc);
        }

        return $return;
    }
}
