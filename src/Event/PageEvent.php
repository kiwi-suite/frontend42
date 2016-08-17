<?php
namespace Frontend42\Event;

use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Provider\PageTypeProvider;
use Zend\EventManager\Event;

class PageEvent extends Event
{
    const EVENT_APPROVED = 'event_approved';
    const EVENT_EDIT_PRE = 'event_edit_pre';
    const EVENT_EDIT_POST = 'event_edit_post';
    const EVENT_ADD_PRE = 'event_add_pre';
    const EVENT_ADD_POST = 'event_add_post';
    const EVENT_DELETE = 'event_delete';

    /**
     * @var PageTypeProvider
     */
    protected $pageTypeProvider;

    /**
     * PageEvent constructor.
     * @param PageTypeProvider $pageTypeProvider
     */
    public function __construct(PageTypeProvider $pageTypeProvider)
    {
        $this->pageTypeProvider = $pageTypeProvider;
    }

    /**
     * @return PageTypeInterface
     */
    public function getPageType()
    {
        return $this->pageTypeProvider->get($this->getSitemap()->getPageType());
    }

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
