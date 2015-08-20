<?php
class Migration20150820151835
{

    public function up(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "CREATE TABLE `frontend42_page_keyword` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `pageId` INT UNSIGNED NOT NULL COMMENT '',
  `keyword` VARCHAR(255) NOT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fgk_page_keyword_idx` (`pageId` ASC)  COMMENT '',
  CONSTRAINT `fgk_page_keyword`
    FOREIGN KEY (`pageId`)
    REFERENCES `skrapid`.`frontend42_page` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)";

        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

    }

    public function down(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "DROP TABLE `frontend42_page_keyword`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

    }


}
