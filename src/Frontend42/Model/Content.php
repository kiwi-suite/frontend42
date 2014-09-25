<?php

namespace Frontend42\Model;

use Core42\Model\AbstractModel;

class Content extends AbstractModel
{

    /**
     * @param int $id
     * @return \Frontend42\Model\Content
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
     * @param int $versionId
     * @return \Frontend42\Model\Content
     */
    public function setVersionId($versionId)
    {
        $this->set('versionId', $versionId);
        return $this;
    }

    /**
     * @return int
     */
    public function getVersionId()
    {
        return $this->get('versionId');
    }

    /**
     * @param string $orderNr
     * @return \Frontend42\Model\Content
     */
    public function setOrderNr($orderNr)
    {
        $this->set('orderNr', $orderNr);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderNr()
    {
        return $this->get('orderNr');
    }

    /**
     * @param string $formType
     * @return \Frontend42\Model\Content
     */
    public function setFormType($formType)
    {
        $this->set('formType', $formType);
        return $this;
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return $this->get('formType');
    }

    /**
     * @param string $content
     * @return \Frontend42\Model\Content
     */
    public function setContent($content)
    {
        $this->set('content', $content);
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->get('content');
    }

    /**
     * @param \DateTime $created
     * @return \Frontend42\Model\Content
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

