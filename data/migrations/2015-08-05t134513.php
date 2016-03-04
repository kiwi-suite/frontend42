<?php
class Migration20150805134513
{

    public function up(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "ALTER TABLE `frontend42_page` ADD COLUMN `excludeMenu` ENUM('true', 'false') NULL DEFAULT 'false' COMMENT '' AFTER `name`";

        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }

    public function down(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "ALTER TABLE `frontend42_page` DROP COLUMN `excludeMenu`";

        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }


}
