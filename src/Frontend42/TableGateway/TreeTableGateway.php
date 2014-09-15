<?php

namespace Frontend42\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class TreeTableGateway extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'frontend42_tree';

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\Tree';

    /**
     * @var array
     */
    protected $databaseTypeMap = array();

    /**
     * @var bool
     */
    protected $underscoreSeparatedKeys = false;


}

