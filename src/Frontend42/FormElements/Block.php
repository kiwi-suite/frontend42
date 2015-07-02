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

    /**
     * @param BlockProvider $blockProvider
     */
    public function setBlockProvider(BlockProvider $blockProvider)
    {
        $this->blockProvider = $blockProvider;
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
        return $this;
    }
}
