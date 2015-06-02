<?php
namespace Frontend42\Selector;

use Core42\Db\ResultSet\ResultSet;
use Core42\Selector\AbstractDatabaseSelector;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

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
        $sql = new Sql($this->getServiceManager()->get('Db\Master'));
        $statement = $sql->prepareStatementForSqlObject($this->getSelect());
        $result = $statement->execute();

        $flatSitemap = [];
        foreach ($result as $_res) {
            $sitemap = $this->getTableGateway('Frontend42\Sitemap')->getHydrator()->hydrate($_res, new Sitemap());
            $sitemap->setId($_res['sitemapId'])->memento();

            $page = $this->getTableGateway('Frontend42\Page')->getHydrator()->hydrate($_res, new Page());

            $flatSitemap[$sitemap->getId()] = [
                'sitemap'       => $sitemap,
                'page'          => $page,
                'children'      => []
            ];
        }


        $sitemap = [];
        foreach ($flatSitemap as &$_item) {
            /** @var Sitemap $model */
            $model = $_item['sitemap'];
            if ($model->getParentId() > 0) {
                $parent =& $flatSitemap[$model->getParentId()];
                $parent['children'][] =&$_item;

                continue;
            }

            $sitemap[] =& $_item;
        }

        return $sitemap;
    }

    /**
     * @return Select|string|ResultSet
     */
    protected function getSelect()
    {
        $sql = new Sql($this->getServiceManager()->get('Db\Master'));
        $select = $sql->select();

        $select->from([
            's' => $this->getTableGateway('Frontend42\Sitemap')->getTable()
        ]);
        $select->join([
            'p' => $this->getTableGateway('Frontend42\Page')->getTable()
        ], "s.id=p.sitemapId");

        $select->where(['p.locale' => $this->locale]);

        $select->order('s.orderNr ASC');

        return $select;
    }
}
