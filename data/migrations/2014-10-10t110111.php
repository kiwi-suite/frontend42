<?php
class Migration20141010110111
{
    public function up(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "CREATE TABLE `frontend42_sitemap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` int(10) unsigned DEFAULT NULL,
  `pageType` varchar(255) NOT NULL,
  `root` enum('true','false') NOT NULL DEFAULT 'false',
  `route` varchar(255) DEFAULT NULL,
  `routeClass` varchar(255) DEFAULT 'segment',
  `defaultParams` varchar(1000) DEFAULT NULL,
  `routeConstraints` varchar(1000) DEFAULT NULL,
  `orderNr` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fgk_sitemap_idx` (`parentId`),
  CONSTRAINT `fgk_sitemap` FOREIGN KEY (`parentId`) REFERENCES `frontend42_sitemap` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "CREATE TABLE `frontend42_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locale` char(6) NOT NULL,
  `sitemapId` int(10) unsigned NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `metaDescription` varchar(255) DEFAULT NULL,
  `metaKeywords` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `publishedFrom` datetime DEFAULT NULL,
  `publishedUntil` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_locale_sitemapId` (`locale`,`sitemapId`),
  KEY `fgk_treelang_sitemapId_idx` (`sitemapId`),
  CONSTRAINT `fgk_sitemap_page` FOREIGN KEY (`sitemapId`) REFERENCES `frontend42_sitemap` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "CREATE TABLE `frontend42_page_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pageId` int(10) unsigned NOT NULL,
  `approved` enum('true','false') NOT NULL DEFAULT 'false',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fgk_version_page_idx` (`pageId`),
  CONSTRAINT `fgk_version_page` FOREIGN KEY (`pageId`) REFERENCES `frontend42_page` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "CREATE TABLE `frontend42_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `versionId` int(10) unsigned NOT NULL,
  `orderNr` smallint(5) unsigned NOT NULL,
  `formType` varchar(255) NOT NULL,
  `content` text,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fgk_content_version_idx` (`versionId`),
  CONSTRAINT `fgk_content_version` FOREIGN KEY (`versionId`) REFERENCES `frontend42_page_version` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }

    public function down(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "DROP TABLE `frontend42_content`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $sql = "DROP TABLE `frontend42_page_version`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $sql = "DROP TABLE `frontend42_page`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $sql = "DROP TABLE `frontend42_sitemap`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }
}
