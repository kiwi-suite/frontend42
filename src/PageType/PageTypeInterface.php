<?php
namespace Frontend42\PageType;


use Frontend42\Model\PageContent;

interface PageTypeInterface
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
     * @return null|string
     */
    public function getHandle();

    /**
     * @param null|string $handle
     * @return $this
     */
    public function setHandle($handle);

    /**
     * @return bool|null
     */
    public function getRoot();

    /**
     * @param bool|null $root
     * @return $this
     */
    public function setRoot($root);

    /**
     * @return array
     */
    public function getSections();

    /**
     * @param array $sections
     * @return $this
     */
    public function setSections(array $sections);

    /**
     * @return array
     */
    public function getProperties();

    /**
     * @param array $properties
     * @return $this
     */
    public function setProperties(array $properties);

    /**
     * @return string
     */
    public function getController();

    /**
     * @param string $controller
     * @return $this
     */
    public function setController($controller);

    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $action
     * @return $this
     */
    public function setAction($action);

    /**
     * @return boolean
     */
    public function isTerminal();

    /**
     * @param boolean $terminal
     * @return $this
     */
    public function setTerminal($terminal);

    /**
     * @return array|null
     */
    public function getAllowedChildren();

    /**
     * @param array|null $allowedChildren
     * @return $this
     */
    public function setAllowedChildren($allowedChildren);

    /**
     * @return array|null
     */
    public function getAllowedParents();

    /**
     * @param array|null $allowedParents
     * @return $this
     */
    public function setAllowedParents($allowedParents);

    /**
     * @return PageContent
     */
    public function getPageContent();
}
