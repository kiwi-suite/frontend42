<?php

namespace Frontend42\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class ContentTableGateway extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'frontend42_content';

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\Content';

    /**
     * @var array
     */
    protected $databaseTypeMap = array();

    /**
     * @var bool
     */
    protected $underscoreSeparatedKeys = false;


}

