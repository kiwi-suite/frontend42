<?php

namespace Frontend42\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class TreeLanguageTableGateway extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'frontend42_tree_language';

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\TreeLanguage';

    /**
     * @var array
     */
    protected $databaseTypeMap = array();

    /**
     * @var bool
     */
    protected $underscoreSeparatedKeys = false;


}

