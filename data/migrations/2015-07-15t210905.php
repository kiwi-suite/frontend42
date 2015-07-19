<?php
class Migration20150715210905
{

    public function up(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "ALTER TABLE `frontend42_sitemap`
ADD COLUMN `updated` DATETIME NOT NULL DEFAULT '2015-01-01 12:00:00' AFTER `handle`,
ADD COLUMN `updatedBy` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `updated`,
ADD COLUMN `created` DATETIME NOT NULL DEFAULT '2015-01-01 12:00:00' AFTER `updatedBy`,
ADD COLUMN `createdBy` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `created`;";

        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        $sql = "ALTER TABLE `frontend42_page`
ADD COLUMN `updated` DATETIME NOT NULL DEFAULT '2015-01-01 12:00:00' AFTER `viewCount`,
ADD COLUMN `updatedBy` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `updated`,
ADD COLUMN `created` DATETIME NOT NULL DEFAULT '2015-01-01 12:00:00' AFTER `updatedBy`,
ADD COLUMN `createdBy` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `created`;";

        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }

    public function down(Zend\ServiceManager\ServiceManager $serviceManager)
    {

    }


}
