<?php
namespace BlockInheritance\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class TableGateway extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'frontend42_block_inheritance';

    /**
     * @var array
     */
    protected $databaseTypeMap = array();

    /**
     * @var string
     */
    protected $modelPrototype = 'BlockInheritance\\Model\\';


}
