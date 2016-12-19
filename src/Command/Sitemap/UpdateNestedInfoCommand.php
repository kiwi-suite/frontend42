<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Core42\I18n\Localization\Localization;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Adapter\Adapter;

class UpdateNestedInfoCommand extends AbstractCommand
{
    /**
     * @return mixed
     */
    protected function execute()
    {
        $tableName = $this->getTableGateway(SitemapTableGateway::class)->getTable();

        $sql = "UPDATE {$tableName} as s INNER JOIN
(SELECT n.id,
         n.nestedLeft,
         COUNT(*)-1 AS level,
         ROUND ((n.nestedRight - n.nestedLeft - 1) / 2) AS offspring
    FROM {$tableName} AS n,
         {$tableName} AS p
   WHERE n.nestedLeft BETWEEN p.nestedLeft AND p.nestedRight
GROUP BY n.id, n.nestedLeft
ORDER BY n.nestedLeft) as sub ON (s.id = sub.id)
SET s.level=sub.level, s.offspring=sub.offspring";

        $adapter = $this->getTableGateway(SitemapTableGateway::class)->getAdapter();
        $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $pageTableName = $this->getTableGateway(PageTableGateway::class)->getTable();

        /** @var Localization $localization */
        $localization = $this->getServiceManager()->get(Localization::class);
        foreach ($localization->getAvailableLocales() as $locale) {
            $sql = "UPDATE {$pageTableName} as p INNER JOIN 
(
SELECT 
 (SELECT GROUP_CONCAT(CONCAT('p', page.id) SEPARATOR '/')
 FROM {$tableName} parent
 INNER JOIN {$pageTableName} as page ON (parent.id = page.sitemapId AND page.locale='{$locale}') 
 WHERE node.nestedLeft >= parent.nestedLeft
 AND node.nestedRight <= parent.nestedRight
 ORDER BY nestedLeft
 ) as route, node.id
FROM {$tableName} node
ORDER BY nestedLeft
) as sub ON (p.sitemapId = sub.id AND p.locale='{$locale}')
SET p.route=sub.route";

            $adapter = $this->getTableGateway(PageTableGateway::class)->getAdapter();
            $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        }

    }
}
