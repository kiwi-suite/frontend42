<?php

namespace Frontend42\Model;

use Core42\Model\AbstractModel;

class Tree extends AbstractModel
{
    /**
     * @param int $id
     * @return \Frontend42\Model\Tree
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $parentId
     * @return \Frontend42\Model\Tree
     */
    public function setParentId($parentId)
    {
        $this->set('parentId', $parentId);
        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->get('route');
    }

    /**
     * @param string $route
     * @return \Frontend42\Model\Tree
     */
    public function setRoute($route)
    {
        $this->set('route', $route);
        return $this;
    }

    /**
     * @return string
     */
    public function getRouteClass()
    {
        return $this->get('routeClass');
    }

    /**
     * @param string $routeClass
     * @return \Frontend42\Model\Tree
     */
    public function setRouteClass($routeClass)
    {
        $this->set('routeClass', $routeClass);
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultParams()
    {
        return $this->get('defaultParams');
    }

    /**
     * @param string $defaultParams
     * @return \Frontend42\Model\Tree
     */
    public function setDefaultParams($defaultParams)
    {
        $this->set('defaultParams', $defaultParams);
        return $this;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->get('parentId');
    }

    /**
     * @param \DateTime $updated
     * @return \Frontend42\Model\Tree
     */
    public function setUpdated($updated)
    {
        $this->set('updated', $updated);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->get('updated');
    }

    /**
     * @param \DateTime $created
     * @return \Frontend42\Model\Tree
     */
    public function setCreated($created)
    {
        $this->set('created', $created);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->get('created');
    }

    /**
     * @param boolean $root
     * @return \Frontend42\Model\Tree
     */
    public function setRoot($root)
    {
        return $this->set('root', $root);
    }

    /**
     * @return boolean
     */
    public function getRoot()
    {
        return $this->get('root');
    }

    /**
     * @param string $pageType
     * @return \Frontend42\Model\Tree
     */
    public function setPageType($pageType)
    {
        return $this->set('pageType', $pageType);
    }

    /**
     * @return boolean
     */
    public function getPageType()
    {
        return $this->get('pageType');
    }
}

