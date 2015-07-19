<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;

/**
 * @method BlockInheritance setSourcePageId() setSourcePageId(int $sourcePageId)
 * @method int getSourcePageId() getSourcePageId()
 * @method BlockInheritance setTargetPageId() setTargetPageId(int $targetPageId)
 * @method int getTargetPageId() getTargetPageId()
 * @method BlockInheritance setSection() setSection(string $section)
 * @method string getSection() getSection()
 */
class BlockInheritance extends AbstractModel
{

    /**
     * @var array
     */
    protected $properties = array(
        'sourcePageId',
        'targetPageId',
        'section',
    );


}
