<?php
class Migration20150703103236
{

    public function up(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "CREATE TABLE `frontend42_page_version` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `versionId` int(10) unsigned NOT NULL,
  `pageId` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  `created` datetime NOT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  `approved` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pageId_versionId` (`pageId`,`versionId`),
  KEY `idx_pageId_approved` (`pageId`,`approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

    }

    public function down(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "DROP TABLE `frontend42_page_version`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

    }


}
