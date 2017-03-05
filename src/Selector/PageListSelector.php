<?php
namespace Frontend42\Selector;

use Core42\Selector\AbstractSelector;
use Core42\Selector\CacheAbleTrait;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Frontend42\View\Helper\Page;
use Zend\Db\Sql\Predicate\Literal;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Where;

class PageListSelector extends AbstractSelector
{
    const SORT_SITEMAP = 'sort_sitemap';


    const SORT_CREATED = 'sort_created';

    /**
     * @var int
     */
    protected $sitemapId;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var null
     */
    protected $limit = null;

    /**
     * @var string
     */
    protected $sort = self::SORT_SITEMAP;

    /**
     * @var int
     */
    protected $sortDirection = SORT_ASC;

    /**
     * @param int $sitemapId
     * @return $this
     */
    public function setSitemapId($sitemapId)
    {
        $this->sitemapId = (int) $sitemapId;
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
     * @param $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableSitemapSort()
    {
        $this->sort = self::SORT_SITEMAP;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableCreatedSort()
    {
        $this->sort = self::SORT_CREATED;

        return $this;
    }

    /**
     * @param $sortDirection
     * @return $this
     */
    public function setSortDirection($sortDirection)
    {
        $this->sortDirection = $sortDirection;

        return $this;
    }

    /**
     * @return array
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
            'parentId' => $this->sitemapId,
            'p.locale' => $this->locale,
            'p.status' => \Frontend42\Model\Page::STATUS_ONLINE,
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

        if (!empty($this->limit)) {
            $select->limit($this->limit);
        }

        $sortDirection = 'ASC';
        if ($this->sortDirection === SORT_DESC) {
            $sortDirection = 'DESC';
        }

        if ($this->sort === self::SORT_SITEMAP) {
            $select->order($sitemapTableName . '.nestedLeft ' . $sortDirection);
        } elseif ($this->sort === self::SORT_CREATED) {
            $select->order(new Literal('IFNULL(p.publishedFrom,p.created) ' . $sortDirection));
        }

        $sql = $this->getTableGateway(SitemapTableGateway::class)->getSql();
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $return = [];
        foreach ($result as $res) {
            $return[] = $res['pageId'];
        }

        return $return;
    }
}
