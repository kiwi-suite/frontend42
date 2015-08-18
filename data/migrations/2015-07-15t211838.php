<?php
class Migration20150715211838
{

    public function up(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "ALTER TABLE `frontend42_sitemap` ADD COLUMN `exclude` ENUM('true', 'false') NULL DEFAULT 'false' AFTER `terminal`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "ALTER TABLE `frontend42_page_version`
ADD CONSTRAINT `fgk_version_page`
  FOREIGN KEY (`pageId`)
  REFERENCES `frontend42_page` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }

    public function down(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "ALTER TABLE `frontend42_sitemap` DROP COLUMN `exclude`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "ALTER TABLE `frontend42_page_version`  DROP FOREIGN KEY `fgk_version_page`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }


}
