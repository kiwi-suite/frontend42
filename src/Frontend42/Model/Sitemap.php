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
 * @method Sitemap setUpdated(\DateTime $updated)
 * @method string getUpdated()
 * @method Sitemap setUpdatedBy(int $updatedBy)
 * @method int getUpdatedBy()
 * @method Sitemap setCreated(\DateTime $created)
 * @method string getCreated()
 * @method Sitemap setCreatedBy(int $createdBy)
 * @method int getCreatedBy()
 */
class Sitemap extends AbstractModel
{

    /**
     * @var array
     */
    protected $properties = array(
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
    );


}
