<?php
namespace Frontend42\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class PageTableGateway extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'frontend42_page';

    /**
     * @var array
     */
    protected $databaseTypeMap = array();

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\Page';
}
