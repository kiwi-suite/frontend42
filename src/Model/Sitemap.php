<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;

/**
 * @method Sitemap setId() setId(int $id)
 * @method int getId() getId()
 * @method Sitemap setParentId() setParentId(int $parentId)
 * @method int getParentId() getParentId()
 * @method Sitemap setOrderNr() setOrderNr(string $orderNr)
 * @method string getOrderNr() getOrderNr()
 * @method Sitemap setPageType() setPageType(string $pageType)
 * @method string getPageType() getPageType()
 * @method Sitemap setTerminal() setTerminal(boolean $terminal)
 * @method boolean getTerminal() getTerminal()
 * @method Sitemap setExclude() setExclude(boolean $exclude)
 * @method boolean getExclude() getExclude()
 * @method Sitemap setLockedFrom() setLockedFrom(\DateTime $lockedFrom)
 * @method \DateTime getLockedFrom() getLockedFrom()
 * @method Sitemap setLockedBy() setLockedBy(int $lockedBy)
 * @method int getLockedBy() getLockedBy()
 * @method Sitemap setHandle() setHandle(string $handle)
 * @method string getHandle() getHandle()
 * @method Sitemap setUpdated() setUpdated(\DateTime $updated)
 * @method \DateTime getUpdated() getUpdated()
 * @method Sitemap setUpdatedBy() setUpdatedBy(int $updatedBy)
 * @method int getUpdatedBy() getUpdatedBy()
 * @method Sitemap setCreated() setCreated(\DateTime $created)
 * @method \DateTime getCreated() getCreated()
 * @method Sitemap setCreatedBy() setCreatedBy(int $createdBy)
 * @method int getCreatedBy() getCreatedBy()
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
        'terminal',
        'exclude',
        'lockedFrom',
        'lockedBy',
        'handle',
        'updated',
        'updatedBy',
        'created',
        'createdBy',
    ];
}
