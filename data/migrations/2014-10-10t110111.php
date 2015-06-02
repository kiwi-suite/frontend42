<?php
class Migration20141010110111
{
    public function up(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "CREATE TABLE `frontend42_sitemap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` int(10) unsigned DEFAULT NULL,
  `orderNr` smallint(5) unsigned NOT NULL,
  `pageType` varchar(255) NOT NULL,
  `terminal` enum('true','false') DEFAULT 'false',
  `lockedFrom` datetime DEFAULT NULL,
  `lockedBy` int(11) DEFAULT NULL,
  `handle` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fgk_sitemap_parentId_idx` (`parentId`),
  CONSTRAINT `fgk_sitemap_parentId` FOREIGN KEY (`parentId`) REFERENCES `frontend42_sitemap` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "CREATE TABLE `frontend42_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sitemapId` int(10) unsigned NOT NULL,
  `locale` varchar(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `publishedFrom` datetime DEFAULT NULL,
  `publishedUntil` datetime DEFAULT NULL,
  `status` enum('online','offline') NOT NULL DEFAULT 'offline',
  `slug` varchar(255) DEFAULT NULL,
  `route` text NOT NULL,
  `viewCount` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sitemap_locale` (`locale`,`sitemapId`),
  KEY `fgk_page_sitemap_idx` (`sitemapId`),
  CONSTRAINT `fgk_page_sitemap` FOREIGN KEY (`sitemapId`) REFERENCES `frontend42_sitemap` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }

    public function down(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "DROP TABLE `frontend42_page`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $sql = "DROP TABLE `frontend42_sitemap`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }
}
