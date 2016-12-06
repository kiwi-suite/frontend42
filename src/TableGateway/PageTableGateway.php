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
     * @var array
     */
    protected $primaryKey = ['id'];

    /**
     * @var array
     */
    protected $databaseTypeMap = [
        'id' => 'integer',
        'sitemapId' => 'integer',
        'locale' => 'string',
        'name' => 'string',
        'publishedFrom' => 'dateTime',
        'publishedUntil' => 'dateTime',
        'status' => 'string',
        'slug' => 'string',
        'route' => 'string',
        'updated' => 'dateTime',
        'created' => 'dateTime',
    ];

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\Page';
}
