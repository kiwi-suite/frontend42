<?php
namespace Frontend42\PageType;

use Frontend42\PageType\PageContent\PageContent;
use Zend\Form\Factory;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill;
use Zend\Stdlib\AbstractOptions;

abstract class AbstractPageType extends AbstractOptions implements PageTypeInterface
{
    /**
     * @var string|null
     */
    protected $handle;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $formDefinition;

    /**
     * @var boolean
     */
    protected $terminal = false;

    /**
     * @var bool
     */
    protected $exclude = false;

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @var FormElementManagerV3Polyfill
     */
    protected $formElementManager;

    /**
     * @return string|null
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return array
     */
    public function getFormDefinition()
    {
        return $this->formDefinition;
    }

    /**
     * @param array $formDefinition
     */
    public function setFormDefinition($formDefinition)
    {
        $this->formDefinition = $formDefinition;
    }

    /**
     * @return boolean
     */
    public function getTerminal()
    {
        return $this->terminal;
    }

    /**
     * @param boolean $terminal
     */
    public function setTerminal($terminal)
    {
        $this->terminal = $terminal;
    }

    /**
     * @return boolean
     */
    public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * @param boolean $exclude
     */
    public function setExclude($exclude)
    {
        $this->exclude = $exclude;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param array $elements
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
    }

    /**
     * @param FormElementManagerV3Polyfill $formElementManager
     */
    public function setFormElementManager(FormElementManagerV3Polyfill $formElementManager)
    {
        $this->formElementManager = $formElementManager;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function getPageForm()
    {
        /** @var Form $form */
        $form =  $this->formElementManager->get(Form::class);

        foreach ($this->getFormDefinition() as $sectionHandle => $subFormInfo) {
            /** @var Fieldset $fieldset */
            $fieldset = $this->formElementManager->get(Fieldset::class);
            $fieldset->setName($sectionHandle);
            $fieldset->setLabel($subFormInfo['label']);

            foreach ($subFormInfo['elements'] as $element) {
                if (empty($this->getElements()[$element])) {
                    throw new \Exception(sprintf("Invalid element '%s' in pageType '%s' ", $element, get_class($this)));
                }
                $fieldset->add($this->getElements()[$element]);
            }

            $form->add($fieldset);
        }

        return $form;
    }

    /**
     * @return PageContent
     */
    public function getPageContent()
    {
        return new PageContent($this->getFormDefinition(), $this->getElements());
    }
}
