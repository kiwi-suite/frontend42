<?php
namespace Frontend42\Block;

use Frontend42\View\Model\BlockModel;

interface BlockInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return array
     */
    public function getElements();

    /**
     * @param array $elements
     * @return $this
     */
    public function setElements($elements);

    /**
     * @param array $values
     * @return BlockModel|array
     */
    public function getViewModel(array $values);
}
