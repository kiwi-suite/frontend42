<?php
namespace Frontend42\Event;

use Frontend42\Model\Page;
use Frontend42\Selector\SlugSelector;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Json\Json;

class PageEventListener extends AbstractListenerAggregate
{
    /**
     * @var SlugSelector
     */
    protected $slugSelector;

    public function __construct(SlugSelector $slugSelector)
    {
        $this->slugSelector = $slugSelector;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $events->attach(PageEvent::EVENT_ADD_PRE, [$this, 'setStandardParams']);
        $events->attach(PageEvent::EVENT_ADD_PRE, [$this, 'getPageRouting']);
        $events->attach(PageEvent::EVENT_EDIT_PRE, [$this, 'setStandardParams']);
        $events->attach(PageEvent::EVENT_EDIT_PRE, [$this, 'getPageRouting']);
    }

    /**
     * @param PageEvent $event
     */
    public function getPageRouting(PageEvent $event)
    {
        /** @var Page $page */
        $page = $event->getTarget();

        $pageType = $event->getPageType();
        $page->setRoute(
            $pageType->getRouting($page, $event->getPageContent(), $event->getSitemap())
        );
    }

    /**
     * @param PageEvent $event
     */
    public function setStandardParams(PageEvent $event)
    {
        /** @var Page $page */
        $page = $event->getTarget();

        $pageContent = $event->getPageContent();

        if ($pageContent->hasParam("name")) {
            $page->setName($pageContent->getParam("name"));
            $page->setSlug($this->slugSelector->setPage($page)->getResult());
        }

        if ($pageContent->hasParam("publishedFrom")) {
            $page->setPublishedFrom($pageContent->getParam("publishedFrom"));
        }

        if ($pageContent->hasParam("publishedUntil")) {
            $page->setPublishedUntil($pageContent->getParam("publishedUntil"));
        }

        if ($pageContent->hasParam("status")
            && in_array($pageContent->getParam("status"), [Page::STATUS_ONLINE, Page::STATUS_OFFLINE])
        ) {
            $page->setStatus($pageContent->getParam("status"));
        }
    }
}
