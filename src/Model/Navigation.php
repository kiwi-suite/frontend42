<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;

/**
 * @method Navigation setPageId() setPageId(int $pageId)
 * @method int getPageId() getPageId()
 * @method Navigation setNav() setNav(string $nav)
 * @method string getNav() getNav()
 */
class Navigation extends AbstractModel
{
    /**
     * @var array
     */
    protected $properties = [
        'pageId',
        'nav',
    ];
}
