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
        'id' => 'Integer',
        'versionId' => 'Integer',
        'pageId' => 'Integer',
        'content' => 'String',
        'created' => 'DateTime',
        'createdBy' => 'Integer',
        'approved' => 'DateTime',
    ];

    /**
     * @var boolean
     */
    protected $useMetaDataFeature = false;

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\PageVersion';
}
