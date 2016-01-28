<?php
namespace Frontend42\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class BlockInheritanceTableGateway extends AbstractTableGateway
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
    protected $modelPrototype = 'Frontend42\\Model\\BlockInheritance';
}
