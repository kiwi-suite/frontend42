<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;

/**
 * @method Sitemap setId() setId(int $id)
 * @method int getId() getId()
 * @method Sitemap setParentId() setParentId(int $parentId)
 * @method int getParentId() getParentId()
 * @method Sitemap setOrderNr() setOrderNr(int $orderNr)
 * @method int getOrderNr() getOrderNr()
 * @method Sitemap setPageType() setPageType(string $pageType)
 * @method string getPageType() getPageType()
 * @method Sitemap setHandle() setHandle(string $handle)
 * @method string getHandle() getHandle()
 */
class Sitemap extends AbstractModel
{

    /**
     * @var array
     */
    public $properties = [
        'id',
        'parentId',
        'orderNr',
        'pageType',
        'handle',
    ];


}
