<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;

class Page extends AbstractModel
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * @param int $id
     * @return \Frontend42\Model\Page
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
     * @param string $locale
     * @return \Frontend42\Model\Page
     */
    public function setLocale($locale)
    {
        $this->set('locale', $locale);
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->get('locale');
    }

    /**
     * @param int $sitemapId
     * @return \Frontend42\Model\Page
     */
    public function setSitemapId($sitemapId)
    {
        $this->set('sitemapId', $sitemapId);
        return $this;
    }

    /**
     * @return int
     */
    public function getSitemapId()
    {
        return $this->get('sitemapId');
    }

    /**
     * @param string $slug
     * @return \Frontend42\Model\Page
     */
    public function setSlug($slug)
    {
        $this->set('slug', $slug);
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->get('slug');
    }

    /**
     * @param string $title
     * @return \Frontend42\Model\Page
     */
    public function setTitle($title)
    {
        $this->set('title', $title);
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @param string $metaDescription
     * @return \Frontend42\Model\Page
     */
    public function setMetaDescription($metaDescription)
    {
        $this->set('metaDescription', $metaDescription);
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->get('metaDescription');
    }

    /**
     * @param string $metaKeywords
     * @return \Frontend42\Model\Page
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->set('metaKeywords', $metaKeywords);
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->get('metaKeywords');
    }

    /**
     * @param string $status
     * @return \Frontend42\Model\Page
     */
    public function setStatus($status)
    {
        $this->set('status', $status);
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @param \DateTime $publishedFrom
     * @return \Frontend42\Model\Page
     */
    public function setPublishedFrom($publishedFrom)
    {
        $this->set('publishedFrom', $publishedFrom);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedFrom()
    {
        return $this->get('publishedFrom');
    }

    /**
     * @param \DateTime $publishedUntil
     * @return \Frontend42\Model\Page
     */
    public function setPublishedUntil($publishedUntil)
    {
        $this->set('publishedUntil', $publishedUntil);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedUntil()
    {
        return $this->get('publishedUntil');
    }


}

