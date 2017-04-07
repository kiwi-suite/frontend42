<?php
namespace Frontend42\Selector\SmartTable;

use Admin42\Selector\SmartTable\AbstractSmartTableSelector;
use Core42\Db\ResultSet\ResultSet;
use Core42\I18n\Localization\Localization;
use Frontend42\Model\Sitemap;
use Frontend42\TableGateway\PageTableGateway;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class FlatSitemapSelector extends AbstractSmartTableSelector
{
    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * @param Sitemap $sitemap
     * @return $this
     */
    public function setSitemap(Sitemap $sitemap)
    {
        $this->sitemap = $sitemap;

        return $this;
    }

    /**
     * @return Select|string|ResultSet
     */
    protected function getSelect()
    {
        $gateway = $this->getTableGateway(PageTableGateway::class);

        $select = $gateway->getSql()->select();
        $select->join(['s' => "frontend42_sitemap"], 's.id = sitemapId', []);

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
     * @return PredicateSet|Where
     */
    protected function getWhere()
    {
        $where = parent::getWhere();

        $selectParent = new Where();
        $selectParent->between('s.nestedLeft', $this->sitemap->getNestedLeft() + 1, $this->sitemap->getNestedRight());

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
        $data = parent::prepareColumns($data);

        /** @var Localization $localization */
        $localization = $this->getServiceManager()->get(Localization::class);

        foreach ($data as $key => $item) {
            $data[$key]['alternateNames'] = [];

            if (empty($item['name']) && count($localization->getAvailableLocales()) > 1) {
                $result = $this->getTableGateway(PageTableGateway::class)->select([
                    'sitemapId' => $item['sitemapId']
                ]);

                foreach ($result as $page) {
                    if ($page->getLocale() === $item['locale']) {
                        continue;
                    }

                    $data[$key]['alternateNames'][] = [
                        'region' => strtolower(\Locale::getRegion($page->getLocale())),
                        'name' => $page->getName(),
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getSearchAbleColumns()
    {
        return ['name', 'locale', 'status'];
    }

    /**
     * @return array
     */
    protected function getSortAbleColumns()
    {
        return ['name', 'created', 'status'];
    }

    /**
     * @return array
     */
    protected function getDisplayColumns()
    {
        return ['id', 'name', 'created', 'status', 'publishedFrom', 'publishedUntil', 'sitemapId', 'locale'];
    }
}
