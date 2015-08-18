<?php
class Migration20150719190255
{

    public function up(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "CREATE TABLE `frontend42_block_inheritance` (
    `sourcePageId` int(10) unsigned NOT NULL,
  `targetPageId` int(10) unsigned NOT NULL,
  `section` varchar(255) NOT NULL,
  PRIMARY KEY (`sourcePageId`,`section`,`targetPageId`),
  KEY `fgk_block_i_target_idx` (`targetPageId`),
  CONSTRAINT `fgk_block_i_source` FOREIGN KEY (`sourcePageId`) REFERENCES `frontend42_page` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fgk_block_i_target` FOREIGN KEY (`targetPageId`) REFERENCES `frontend42_page` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        ;

    }

    public function down(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "DROP TABLE `frontend42_block_inheritance`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }


}
