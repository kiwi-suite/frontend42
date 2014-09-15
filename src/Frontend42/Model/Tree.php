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
    public function getController()
    {
        return $this->get('controller');
    }

    /**
     * @param string $controller
     * @return \Frontend42\Model\Tree
     */
    public function setController($controller)
    {
        $this->set('controller', $controller);
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->get('action');
    }

    /**
     * @param string $action
     * @return \Frontend42\Model\Tree
     */
    public function setAction($action)
    {
        $this->set('action', $action);
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
}

