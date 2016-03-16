<?php
namespace Frontend42\Selector;

use Core42\Db\ResultSet\ResultSet;
use Core42\Permission\Rbac\AuthorizationService;
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
     * @var bool
     */
    protected $includeExclude = true;

    /**
     * @var bool
     */
    protected $includeOffline = true;

    /**
     * @var bool
     */
    protected $includeExcludedFromMenu = true;

    /**
     * @var bool
     */
    protected $authorizationCheck = false;

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
     * @param bool $includeExclude
     * @return $this
     */
    public function setIncludeExclude($includeExclude)
    {
        $this->includeExclude = $includeExclude;

        return $this;
    }

    /**
     * @param bool $includeOffline
     * @return $this
     */
    public function setIncludeOffline($includeOffline)
    {
        $this->includeOffline = $includeOffline;

        return $this;
    }

    /**
     * @param bool $includeExcludedFromMenu
     * @return $this
     */
    public function setIncludeExcludeFromMenu($includeExcludedFromMenu)
    {
        $this->includeExcludedFromMenu = $includeExcludedFromMenu;

        return $this;
    }

    /**
     * @param bool $authorizationCheck
     * @return $this
     */
    public function setAuthorizationCheck($authorizationCheck)
    {
        $this->authorizationCheck = (boolean) $authorizationCheck;

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

            if (!isset($_item['sitemap'])) {
                continue;
            }
            $model = $_item['sitemap'];
            if ($model->getParentId() > 0) {
                $parent =& $flatSitemap[$model->getParentId()];
                $parent['children'][] =&$_item;

                continue;
            }

            $sitemap[] =& $_item;
        }

        if ($this->authorizationCheck === true) {
            return $this->getTreePart($sitemap);
        }

        return $sitemap;
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
        $sql = new Sql($this->getServiceManager()->get('Db\Master'));
        $select = $sql->select();

        $select->from([
            's' => $this->getTableGateway('Frontend42\Sitemap')->getTable()
        ]);
        $select->join([
            'p' => $this->getTableGateway('Frontend42\Page')->getTable()
        ], "s.id=p.sitemapId");

        $select->where(['p.locale' => $this->locale]);

        if ($this->includeExclude === false) {
            $select->where(['s.exclude' => 'false']);
        }

        if ($this->includeOffline === false) {
            $select->where(['p.status' => 'online']);
        }

        if ($this->includeExcludedFromMenu === false) {
            $select->where(['p.excludeMenu' => 'false']);
        }

        $select->order('s.orderNr ASC');

        return $select;
    }
}
