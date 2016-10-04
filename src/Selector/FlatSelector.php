<?php
namespace Frontend42\Selector;

use Admin42\Selector\SmartTable\AbstractSmartTableSelector;
use Core42\Db\ResultSet\ResultSet;
use Frontend42\TableGateway\PageTableGateway;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class FlatSelector extends AbstractSmartTableSelector
{
    /**
     * @var int
     */
    protected $sitemapId;

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
     * @return Select|string|ResultSet
     */
    protected function getSelect()
    {
        $gateway = $this->getTableGateway(PageTableGateway::class);

        $select = $gateway->getSql()->select();
        $select->join(['s' => "frontend42_sitemap"], 's.id = sitemapId', ['handle']);

        $where = $this->getWhere();
        if (!empty($where)) {
            $select->where($where);
        }

        $order = $this->getOrder();
        if (!empty($order)) {
            $select->order($order);
        } else {
            $select->order('frontend42_page.created DESC');
        }

        return $select;
    }

    /**
     * @return array
     */
    protected function getDatabaseTypeMap()
    {
        return [
            'id' => 'integer',
            'created' => 'datetime',
            'publishedFrom' => 'datetime',
            'publishedUntil' => 'datetime',
        ];
    }

    /**
     * @return PredicateSet|Where
     */
    protected function getWhere()
    {
        $where = parent::getWhere();

        $selectParent = new Where();
        $selectParent->equalTo("s.parentId", (int) $this->sitemapId);

        if (empty($where)) {
            return $selectParent;
        }

        return new PredicateSet([$where, $selectParent], PredicateSet::COMBINED_BY_AND);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareColumns(array $data)
    {
        $newData = parent::prepareColumns($data);

        foreach ($newData as $key => $array) {
            $alternateNames = [];
            if (empty($array['name'])) {
                $result = $this
                    ->getTableGateway(PageTableGateway::class)
                    ->select([
                        'sitemapId' => $array['sitemapId']
                    ]);
                foreach ($result as $_page) {
                    if (strlen($_page->getName()) == 0) {
                        continue;
                    }
                    $alternateNames[] = [
                        'locale' => $_page->getLocale(),
                        'region' => strtolower(\Locale::getRegion($_page->getLocale())),
                        'title'  => $_page->getName()
                    ];
                }
            }
            $newData[$key]['alternateNames'] = $alternateNames;
        }

        return $newData;
    }

    /**
     * @return array
     */
    protected function getSearchAbleColumns()
    {
        return ['frontend42_page.id', 'name', 'locale', 'status'];
    }

    /**
     * @return array
     */
    protected function getSortAbleColumns()
    {
        return ['id', 'sitemapId', 'name', 'created', 'publishedFrom', 'publishedUntil'];
    }

    /**
     * @return array
     */
    protected function getDisplayColumns()
    {
        return ['id', 'sitemapId', 'name', 'created', 'publishedFrom', 'publishedUntil', 'status'];
    }
}
