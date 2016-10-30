<?php
namespace Frontend42\Selector;

use Core42\Selector\AbstractSelector;
use Core42\Selector\CacheAbleTrait;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;

class RoutingSelector extends AbstractSelector
{
    use CacheAbleTrait;

    /**
     * @return string
     */
    protected function getCacheName()
    {
        return 'routing';
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return 'routing';
    }

    /**
     * @return array
     */
    protected function getUncachedResult()
    {
        $flat = $this->getFlat();
        $hash = [];
        $routing = [];

        foreach ($flat as $spec) {
            foreach ($spec['pageRouting'] as $locale => $pageRoutes) {
                $routeName = "p" . $pageRoutes['pageId'];
                $hash[$spec['sitemapId']][$locale] = $pageRoutes['routing'];

                if ($pageRoutes['routing'] === false) {
                    continue;
                }

                if ($spec['parentId'] === null) {
                    $routing[$routeName] =& $hash[$spec['sitemapId']][$locale];
                    continue;
                }

                if ($hash[$spec['parentId']][$locale] === false) {
                    continue;
                }

                if (!isset($hash[$spec['parentId']][$locale]['child_routes'])) {
                    $hash[$spec['parentId']][$locale]['child_routes'] = [];
                }

                if (!array_key_exists("may_terminate", $hash[$spec['parentId']][$locale])) {
                    $hash[$spec['parentId']][$locale]['may_terminate'] = true;
                }

                $hash[$spec['parentId']][$locale]['child_routes'][$routeName] =& $hash[$spec['sitemapId']][$locale];
            }
        }

        return $routing;
    }

    /**
     * @return array
     */
    protected function getFlat()
    {
        $sitemapTableName = $this->getTableGateway(SitemapTableGateway::class)->getTable();
        $sql = $this->getTableGateway(SitemapTableGateway::class)->getSql();
        $select = $sql->select();
        $select->columns(['pageType', 'parentId']);

        $select->join(
            ['p' => $this->getTableGateway(PageTableGateway::class)->getTable()],
            "{$sitemapTableName}.id=p.sitemapId",
            ['pageId' => 'id', 'sitemapId', 'locale']
        );

        $select->order($sitemapTableName.'.orderNr ASC');

        $flat = [];
        $sql = $this->getTableGateway(SitemapTableGateway::class)->getSql();
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        foreach ($result as $res) {
            if (!isset($flat[$res['sitemapId']])) {
                $flat[$res['sitemapId']] = [
                    'sitemapId' => $res['sitemapId'],
                    'parentId' => $res['parentId'],
                    'pageRouting' => [],
                ];
            }

            /** @var PageTypeInterface $pageType */
            $pageType = $this->getServiceManager()->get(PageTypePluginManager::class)->get($res['pageType']);

            $page = $this->getSelector(PageSelector::class)->setPageId($res['pageId'])->getResult();
            if (empty($page)) {
                continue;
            }

            $routing = $pageType->getRouting($page);
            $flat[$res['sitemapId']]['pageRouting'][$res['locale']] = [
                'routing' => $routing,
                'pageId' => $page->getId(),
            ];
        }

        return $flat;
    }
}
