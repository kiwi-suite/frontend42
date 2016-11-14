<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;

/**
 * @method Block setId() setId(string $id)
 * @method string getId() getId()
 * @method Block setIndex() setIndex(int $index)
 * @method int getIndex() getIndex()
 * @method Block setType() setType(string $type)
 * @method string getType() getType()
 * @method Block setName() setName(string $name)
 * @method string getName() getName()
 * @method Block setElements() setElements(array $elements)
 * @method array getElements() getElements()
 */
class Block extends AbstractModel
{
    /**
     * @var array
     */
    public $properties = [
        'id',
        'index',
        'type',
        'name',
        'elements'
    ];
}
