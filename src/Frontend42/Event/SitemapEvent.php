<?php
namespace Frontend42\Event;

use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Zend\EventManager\Event;

class SitemapEvent extends Event
{
    const EVENT_APPROVED = 'event_approved';
    const EVENT_EDIT_PRE = 'event_edit_pre';
    const EVENT_EDIT_POST = 'event_edit_post';
    const EVENT_ADD = 'event_add';
    const EVENT_DELETE = 'event_delete';
    const EVENT_CHANGE_PAGETYPE = 'event_change_pagetype';
    const EVENT_SORTING_CHANGE = 'event_sorting_change';

    /**
     * @return PageTypeInterface
     */
    public function getPageType()
    {
        return $this->getParam('pageType');
    }

    /**
     * @return Sitemap
     */
    public function getSitemap()
    {
        return $this->getParam('sitemap');
    }
}
