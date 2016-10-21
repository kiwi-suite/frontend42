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
        'orderNr' => 'integer',
        'pageType' => 'string',
        'handle' => 'string',
    ];

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\Sitemap';


}
