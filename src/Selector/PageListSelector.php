<?php
namespace Frontend42\Selector;

use Core42\Selector\AbstractSelector;
use Core42\Selector\CacheAbleTrait;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Frontend42\View\Helper\Page;

class PageListSelector extends AbstractSelector
{
    use CacheAbleTrait;

    /**
     * @var int
     */
    protected $sitemapId;

    /**
     * @var string
     */
    protected $locale;


    /**
     * @param int $sitemapId
     * @return $this
     */
    public function setSitemapId($sitemapId)
    {
        $this->sitemapId = $sitemapId;
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
     * @return string
     */
    protected function getCacheName()
    {
        return "page";
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return "pagelist" . $this->sitemapId . $this->locale;
    }

    /**
     * @return array
     */
    protected function getUncachedResult()
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
            'p.locale' => $this->locale
        ]);

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
