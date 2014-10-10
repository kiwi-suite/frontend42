<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class PageVersionTableGateway extends AbstractTableGateway
{

    /**
     * @var string
     */
    protected $table = 'frontend42_page_version';

    /**
     * @var string
     */
    protected $modelPrototype = 'Frontend42\\Model\\PageVersion';

    /**
     * @var array
     */
    protected $databaseTypeMap = array();

    /**
     * @var bool
     */
    protected $underscoreSeparatedKeys = false;
}
