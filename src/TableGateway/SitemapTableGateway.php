<?php
namespace Frontend42\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class SitemapTableGateway extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'frontend42_sitemap';

    /**
     * @var array
     */
    protected $primaryKey = ['id'];

    /**
     * @var array
     */
    protected $databaseTypeMap = [
        'id' => 'integer',
        'parentId' => 'integer',
        'nestedLeft' => 'integer',
        'nestedRight' => 'integer',
        'pageType' => 'string',
        'handle' => 'string',
        'offspring' => 'integer',
        'level' => 'integer',
    ];

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\Sitemap';


}
