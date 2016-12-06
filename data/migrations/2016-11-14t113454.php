<?php
class Migration20161114113454
{

    public function up(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "CREATE TABLE `frontend42_sitemap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` int(10) unsigned DEFAULT NULL,
  `nestedLeft` int(10) unsigned DEFAULT NULL,
  `nestedRight` int(10) unsigned DEFAULT NULL,
  `pageType` varchar(255) NOT NULL,
  `handle` varchar(255) DEFAULT NULL,
  `level` int(10) unsigned DEFAULT NULL,
  `offspring` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_nested` (`nestedLeft`,`nestedRight`),
  KEY `idx_parent` (`parentId`,`nestedLeft`,`level`),
  KEY `idx_level` (`level`,`parentId`,`nestedLeft`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "CREATE TABLE `frontend42_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sitemapId` int(10) unsigned NOT NULL,
  `locale` varchar(10) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `publishedFrom` datetime DEFAULT NULL,
  `publishedUntil` datetime DEFAULT NULL,
  `status` enum('online','offline') NOT NULL DEFAULT 'offline',
  `slug` varchar(255) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sitemap_locale` (`sitemapId`,`locale`),
  CONSTRAINT `fgk_sitemap` FOREIGN KEY (`sitemapId`) REFERENCES `frontend42_sitemap` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "CREATE TABLE `frontend42_page_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `versionName` int(10) unsigned NOT NULL,
  `pageId` int(10) unsigned NOT NULL,
  `content` longtext NOT NULL,
  `approved` datetime DEFAULT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pageId_versionName` (`pageId`,`versionName`),
  KEY `idx_pageId_approved` (`pageId`,`approved`),
  CONSTRAINT `fgk_page_version` FOREIGN KEY (`pageId`) REFERENCES `frontend42_page` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "CREATE TABLE `frontend42_navigation` (
  `pageId` int(10) unsigned NOT NULL,
  `nav` varchar(255) NOT NULL,
  PRIMARY KEY (`pageId`,`nav`),
  CONSTRAINT `fgk_page_nav` FOREIGN KEY (`pageId`) REFERENCES `frontend42_page` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

    }

    public function down(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "DROP TABLE `frontend42_navigation`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "DROP TABLE `frontend42_page_version`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "DROP TABLE `frontend42_page`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "DROP TABLE `frontend42_sitemap`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }


}
