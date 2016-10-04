<?php
namespace Frontend42\PageType;

use Frontend42\Controller\ContentController;
use Frontend42\PageType\PageContent\PageContent;
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
    protected $controller = ContentController::class;

    /**
     * @var string
     */
    protected $action = "index";

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

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
    protected $options = [];

    /**
     * @var array
     */
    protected $sections = [];

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
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * @return PageContent
     */
    public function getPageContent()
    {
        return new PageContent();
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param array $sections
     */
    public function setSections($sections)
    {
        $this->sections = $sections;
    }
}
