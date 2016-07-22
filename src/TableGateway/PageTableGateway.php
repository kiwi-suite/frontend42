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
        'id' => 'Integer',
        'sitemapId' => 'Integer',
        'locale' => 'String',
        'name' => 'String',
        'excludeMenu' => 'Boolean',
        'publishedFrom' => 'DateTime',
        'publishedUntil' => 'DateTime',
        'status' => 'String',
        'slug' => 'String',
        'route' => 'String',
        'viewCount' => 'Integer',
        'updated' => 'DateTime',
        'updatedBy' => 'Integer',
        'created' => 'DateTime',
        'createdBy' => 'Integer',
    ];

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\Page';
}
