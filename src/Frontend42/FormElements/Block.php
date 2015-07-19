<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\FormElements;

use Admin42\FormElements\Dynamic;
use Frontend42\Block\BlockProvider;

class Block extends Dynamic
{
    /**
     * @var BlockProvider
     */
    protected $blockProvider;

    /**
     * @var array
     */
    protected $availableBlocks = [];

    protected $enableInheritance = false;

    protected $interName;

    /**
     * @param BlockProvider $blockProvider
     */
    public function setBlockProvider(BlockProvider $blockProvider)
    {
        $this->blockProvider = $blockProvider;
    }

    /**
     * @param string $name
     * @return \Zend\Form\Element|\Zend\Form\ElementInterface
     */
    public function setName($name)
    {
        if ($this->interName === null) {
            $this->interName = $name;
        }

        return parent::setName($name);
    }

    /**
     * @param array|\Traversable $options
     * @return $this
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['available_blocks'])) {
            $this->availableBlocks = $options['available_blocks'];
            foreach ($this->availableBlocks as $blockType) {
                $this->addTargetElement($blockType, $this->blockProvider->getBlockForm($blockType));
            }
        }

        if (isset($options['enable_inheritance'])) {
            $this->enableInheritance = $options['enable_inheritance'];
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInternName()
    {
        return $this->interName;
    }

    /**
     * @return bool
     */
    public function getEnableInheritance()
    {
        return $this->enableInheritance;
    }
}
