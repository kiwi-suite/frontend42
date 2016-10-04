<?php
namespace Frontend42\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class PageKeywordTableGateway extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'frontend42_page_keyword';

    /**
     * @var array
     */
    protected $primaryKey = ['id'];

    /**
     * @var array
     */
    protected $databaseTypeMap = [
        'id' => 'integer',
        'pageId' => 'integer',
        'keyword' => 'string',
    ];

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\PageKeyword';
}
