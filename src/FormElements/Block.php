<?php
namespace Frontend42\FormElements;

use Admin42\FormElements\Stack;

class Block extends Stack
{
    protected $blockConfig = [];
    /**
     * Block constructor.
     * @param null $name
     * @param array $options
     * @param array $blockConfig
     */
    public function __construct($name = null, $options = [], array $blockConfig = [])
    {
        parent::__construct($name, $options);

        $this->blockConfig = $blockConfig;
    }

    /**
     * @param array|\Traversable $options
     * @return $this
     */
    public function setOptions($options)
    {
        if (isset($options['available']) && is_array($options['available'])) {
            foreach ($options['available'] as $block) {
                $spec = (isset($this->blockConfig[$block])) ? $this->blockConfig[$block] : [];
                if (empty($spec)) {
                    continue;
                }

                $element = $this->getFormFactory()->createFieldset($spec);
                $this->addProtoType($element->getName(), $element);
            }
        }

        return $this;
    }
}
