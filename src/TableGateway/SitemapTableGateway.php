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
        'id' => 'Integer',
        'parentId' => 'Integer',
        'orderNr' => 'Integer',
        'pageType' => 'String',
        'terminal' => 'Boolean',
        'exclude' => 'Boolean',
        'lockedFrom' => 'DateTime',
        'lockedBy' => 'Integer',
        'handle' => 'String',
        'updated' => 'DateTime',
        'updatedBy' => 'Integer',
        'created' => 'DateTime',
        'createdBy' => 'Integer',
    ];

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\Sitemap';
}
