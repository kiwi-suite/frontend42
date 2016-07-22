<?php
namespace Frontend42\Block;

use Zend\Form\Factory;
use Zend\Form\Fieldset;
use Zend\Form\FormElementManager;
use Zend\Stdlib\Glob;

class BlockProvider
{
    /**
     * @var array
     */
    protected $blockTypes;

    /**
     * @var FormElementManager
     */
    protected $formElementManager;

    /**
     * @param $blockTypePaths
     */
    public function loadBlockTypes($blockTypePaths)
    {
        foreach ($blockTypePaths as $path) {
            $entries = Glob::glob($path);
            foreach ($entries as $file) {
                $blockTypeOptions = new BlockOptions(require_once $file);

                $this->blockTypes[$blockTypeOptions->getHandle()] = $blockTypeOptions;
            }
        }
    }

    /**
     * @param FormElementManager $formElementManager
     */
    public function setFormElementManager(FormElementManager $formElementManager)
    {
        $this->formElementManager = $formElementManager;
    }

    /**
     * @param string $handle
     * @return Form
     */
    public function getBlockForm($handle)
    {
        /** @var BlockOptions $blockOptions */
        $blockOptions = $this->blockTypes[$handle];

        $fieldset = new Fieldset($handle);
        $fieldset->setFormFactory(new Factory($this->formElementManager));
        $fieldset->setLabel($blockOptions->getName());

        $forms = $blockOptions->getForm();

        foreach ($forms as $spec) {
            $fieldset->add($spec);
        }

        return $fieldset;
    }

    /**
     * @param string $name
     * @param array $spec
     * @return Fieldset
     */
    public function getVirtualBlockForm($name, $label, array $spec)
    {
        $fieldset = new Fieldset($name);
        $fieldset->setFormFactory(new Factory($this->formElementManager));
        $fieldset->setLabel($label);

        foreach ($spec as $formSpec) {
            $fieldset->add($formSpec);
        }

        return $fieldset;
    }
}
