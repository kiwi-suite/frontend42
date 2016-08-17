<?php
namespace Frontend42\Event;

use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Zend\EventManager\Event;

class SitemapEvent extends Event
{
    const EVENT_ADD = 'event_add';
    const EVENT_CHANGE_PAGETYPE = 'event_change_pagetype';
    const EVENT_SORTING_CHANGE = 'event_sorting_change';
    const EVENT_GENERATE_SITEMAP = 'event_generate_sitemap';

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
