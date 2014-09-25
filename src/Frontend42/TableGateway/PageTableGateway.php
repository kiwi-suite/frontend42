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
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\Page';

    /**
     * @var array
     */
    protected $databaseTypeMap = array();

    /**
     * @var bool
     */
    protected $underscoreSeparatedKeys = false;


}

