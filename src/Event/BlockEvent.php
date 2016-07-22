<?php
namespace Frontend42\Event;

use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Zend\EventManager\Event;

class BlockEvent extends Event
{
    const EVENT_ADD_INHERITANCE = 'event_add_inheritance';
    const EVENT_DELETE_INHERITANCE = 'event_delete_inheritance';
}
