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
     * @param int $treeLanguageId
     * @return \Frontend42\Model\Content
     */
    public function setTreeLanguageId($treeLanguageId)
    {
        $this->set('treeLanguageId', $treeLanguageId);
        return $this;
    }

    /**
     * @return int
     */
    public function getTreeLanguageId()
    {
        return $this->get('treeLanguageId');
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


}

