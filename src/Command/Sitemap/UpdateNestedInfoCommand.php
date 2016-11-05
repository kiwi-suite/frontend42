<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
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
         COUNT(*)-1 AS level,
         ROUND ((n.nestedRight - n.nestedLeft - 1) / 2) AS offspring
    FROM {$tableName} AS n,
         {$tableName} AS p
   WHERE n.nestedLeft BETWEEN p.nestedLeft AND p.nestedRight
GROUP BY n.nestedLeft
ORDER BY n.nestedLeft) as sub ON (s.id = sub.id)
SET s.level=sub.level, s.offspring=sub.offspring";

        $adapter = $this->getTableGateway(SitemapTableGateway::class)->getAdapter();
        $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
    }
}
