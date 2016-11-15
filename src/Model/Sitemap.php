<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;

/**
 * @method Sitemap setId() setId(int $id)
 * @method int getId() getId()
 * @method Sitemap setParentId() setParentId(int $parentId)
 * @method int getParentId() getParentId()
 * @method Sitemap setNestedLeft() setNestedLeft(int $nestedLeft)
 * @method int getNestedLeft() getNestedLeft()
 * @method Sitemap setNestedRight() setNestedRight(int $nestedRight)
 * @method int getNestedRight() getNestedRight()
 * @method Sitemap setPageType() setPageType(string $pageType)
 * @method string getPageType() getPageType()
 * @method Sitemap setHandle() setHandle(string $handle)
 * @method string getHandle() getHandle()
 * @method Sitemap setOffspring() setOffspring(int $offspring)
 * @method int getOffspring() getOffspring()
 * @method Sitemap setLevel() setLevel(int $level)
 * @method int getLevel() getLevel()
 */
class Sitemap extends AbstractModel
{

    /**
     * @var array
     */
    public $properties = [
        'id',
        'parentId',
        'nestedLeft',
        'nestedRight',
        'pageType',
        'handle',
        'offspring',
        'level',
    ];


}
