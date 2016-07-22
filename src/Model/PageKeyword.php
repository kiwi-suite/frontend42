<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;

/**
 * @method PageKeyword setId() setId(int $id)
 * @method int getId() getId()
 * @method PageKeyword setPageId() setPageId(int $pageId)
 * @method int getPageId() getPageId()
 * @method PageKeyword setKeyword() setKeyword(string $keyword)
 * @method string getKeyword() getKeyword()
 */
class PageKeyword extends AbstractModel
{

    /**
     * @var array
     */
    public $properties = [
        'id',
        'pageId',
        'keyword',
    ];
}
