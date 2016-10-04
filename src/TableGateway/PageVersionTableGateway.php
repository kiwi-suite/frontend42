<?php
namespace Frontend42\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class PageVersionTableGateway extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'frontend42_page_version';

    /**
     * @var array
     */
    protected $primaryKey = ['id'];

    /**
     * @var array
     */
    protected $databaseTypeMap = [
        'id' => 'integer',
        'versionId' => 'integer',
        'pageId' => 'integer',
        'content' => 'json',
        'created' => 'dateTime',
        'createdBy' => 'integer',
        'approved' => 'dateTime',
    ];

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\PageVersion';
}
