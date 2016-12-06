<?php
namespace Frontend42\Event;

use Frontend42\Model\PageContent;
use Frontend42\Model\Sitemap;
use Zend\EventManager\Event;

class PageEvent extends Event
{
    const EVENT_APPROVED = 'approved';
    const EVENT_EDIT_PRE = 'edit.pre';
    const EVENT_EDIT_POST = 'edit.post';
    const EVENT_ADD_PRE = 'add.pre';
    const EVENT_ADD_POST = 'add.post';
    const EVENT_DELETE = 'delete';

    /**
     * @return Sitemap
     */
    public function getSitemap()
    {
        return $this->getParam('sitemap');
    }

    /**
     * @return boolean
     */
    public function getApproved()
    {
        return $this->getParam("approved");
    }

    /**
     * @return PageContent
     */
    public function getPageContent()
    {
        return $this->getParam("pageContent");
    }
}
