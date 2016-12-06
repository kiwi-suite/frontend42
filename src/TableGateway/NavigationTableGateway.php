<?php
namespace Frontend42\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class NavigationTableGateway extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'frontend42_navigation';

    /**
     * @var array
     */
    protected $primaryKey = ['pageId', 'nav'];

    /**
     * @var array
     */
    protected $databaseTypeMap = [
        'pageId' => 'integer',
        'nav' => 'string',
    ];

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\Navigation';
}
