<?php

namespace Frontend42\Model;

use Core42\Model\AbstractModel;

class PageVersion extends AbstractModel
{

    /**
     * @param int $id
     * @return \Frontend42\Model\PageVersion
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
     * @param int $pageId
     * @return \Frontend42\Model\PageVersion
     */
    public function setPageId($pageId)
    {
        $this->set('pageId', $pageId);
        return $this;
    }

    /**
     * @return int
     */
    public function getPageId()
    {
        return $this->get('pageId');
    }

    /**
     * @param boolean $approved
     * @return \Frontend42\Model\PageVersion
     */
    public function setApproved($approved)
    {
        $this->set('approved', $approved);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getApproved()
    {
        return $this->get('approved');
    }

    /**
     * @param \DateTime $created
     * @return \Frontend42\Model\PageVersion
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


}

