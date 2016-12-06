<?php
namespace Frontend42\PageType;

use Core42\Hydrator\Mutator\Mutator;
use Frontend42\Model\Page;
use Frontend42\Model\PageContent;
use Zend\Stdlib\AbstractOptions;

abstract class AbstractPageType extends AbstractOptions implements PageTypeInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string|null
     */
    protected $handle = null;

    /**
     * @var bool|null
     */
    protected $root = null;

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @var array
     */
    protected $sections = [];

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var bool
     */
    protected $terminal = false;

    /**
     * @var null|array
     */
    protected $allowedChildren = null;

    /**
     * @var null|array
     */
    protected $allowedParents = null;

    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var Mutator
     */
    protected $mutator;

    /**
     * @var bool
     */
    protected $sorting = true;

    /**
     * @var string
     */
    protected $layout = 'layout/layout';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param null|string $handle
     * @return $this
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param bool|null $root
     * @return $this
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
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
     * @return $this
     */
    public function setSections(array $sections)
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     * @return $this
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;

        return $this;
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
     * @return $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
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
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTerminal()
    {
        return $this->terminal;
    }

    /**
     * @param boolean $terminal
     * @return $this
     */
    public function setTerminal($terminal)
    {
        $this->terminal = $terminal;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getAllowedChildren()
    {
        return $this->allowedChildren;
    }

    /**
     * @param array|null $allowedChildren
     * @return $this
     */
    public function setAllowedChildren($allowedChildren)
    {
        $this->allowedChildren = $allowedChildren;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getAllowedParents()
    {
        return $this->allowedParents;
    }

    /**
     * @param array|null $allowedParents
     * @return $this
     */
    public function setAllowedParents($allowedParents)
    {
        $this->allowedParents = $allowedParents;
        return $this;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param array $defaults
     * @return $this
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSorting()
    {
        return $this->sorting;
    }

    /**
     * @param boolean $sorting
     * @return $this
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;

        return $this;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     * @return AbstractPageType
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @param array $content
     * @param Page $page
     * @return PageContent
     */
    public function getPageContent(array $content = [], Page $page = null)
    {
        $properties = [];

        foreach ($this->getSections() as $section) {
            if (empty($section['elements'])) {
                continue;
            }

            foreach ($section['elements'] as $element) {
                if (empty($element['name'])) {
                    continue;
                }

                $properties[] = $element['name'];
            }
        }

        $autoFilledForcedProperties = [];
        foreach (['name', 'status', 'publishedFrom', 'publishedUntil', 'slug'] as $forcedProperty) {
            if (!in_array($forcedProperty, $properties)) {
                $properties[] = $forcedProperty;
            }

            if (!array_key_exists($forcedProperty, $content) && $page !== null) {
                $getter = "get" . ucfirst($forcedProperty);
                $autoFilledForcedProperties[] = $forcedProperty;
                $content[$forcedProperty] = $page->{$getter}();
            }
        }

        $pageContent = new PageContent($properties);
        $pageContent->populate($content);
        $pageContent->memento();
        foreach ($autoFilledForcedProperties as $property) {
            $pageContent->addAutoFilledProperty($property);
        }

        return $pageContent;
    }

    /**
     * @param Mutator $mutator
     * @return $this
     */
    public function setMutator(Mutator $mutator)
    {
        $this->mutator = $mutator;

        return $this;
    }

    /**
     * @param PageContent $pageContent
     * @return PageContent
     */
    public function mutate(PageContent $pageContent)
    {
        $spec = [];

        foreach ($this->getSections() as $section) {
            if (empty($section['elements'])) {
                continue;
            }

            foreach ($section['elements'] as $element) {
                if (empty($element['name'])) {
                    continue;
                }

                $spec[] = [
                    'name' => $element['name'],
                    'type' => $element['type'],
                ];
            }
        }

        foreach ($this->getDefaults() as $default) {
            if (empty($default['name'])) {
                continue;
            }

            $spec[] = [
                'name' => $default['name'],
                'type' => $default['type'],
            ];
        }

        $content = $this->mutator->hydrate($pageContent->toArray(), $spec);

        $pageContent = new PageContent(array_keys($content));
        $pageContent->populate($content);
        $pageContent->memento();

        return $pageContent;
    }
}
