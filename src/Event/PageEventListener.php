<?php
namespace Frontend42\Event;

use Core42\Stdlib\DefaultGetterTrait;
use Frontend42\Model\Page;
use Frontend42\Selector\SlugSelector;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceManager;

class PageEventListener extends AbstractListenerAggregate
{
    use DefaultGetterTrait;

    /**
     * PageEventListener constructor.
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
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
        $events->attach(PageEvent::EVENT_ADD_PRE, [$this, 'setSlug']);
        $events->attach(PageEvent::EVENT_EDIT_PRE, [$this, 'setStandardParams']);
        $events->attach(PageEvent::EVENT_EDIT_PRE, [$this, 'setSlug']);
        $events->attach(PageEvent::EVENT_APPROVED, [$this, 'setStandardParams']);
        $events->attach(PageEvent::EVENT_APPROVED, [$this, 'setSlug']);
    }

    /**
     * @param PageEvent $event
     */
    public function setSlug(PageEvent $event)
    {
        /** @var Page $page */
        $page = $event->getTarget();

        $pageContent = $event->getPageContent();

        if (!$pageContent->hasAutoFilledProperty('slug')) {
            $slug = $pageContent->getSlug();
            if (empty($slug)) {
                $slug = $page->getName();
                $pageContent->setSlug($slug);
            }
            $slug = $this->getSelector(SlugSelector::class)->setPage($page)->setSlug($slug)->getResult();
        } else {
            $slug = $this->getSelector(SlugSelector::class)
                ->setPage($page)
                ->setSlug($page->getName())
                ->getResult();
            $pageContent->setSlug($slug);
        }

        if ($event->getApproved() === true) {
            $page->setSlug($slug);
        }
    }

    /**
     * @param PageEvent $event
     */
    public function setStandardParams(PageEvent $event)
    {
        if ($event->getApproved() === false) {
            return;
        }

        /** @var Page $page */
        $page = $event->getTarget();

        $pageContent = $event->getPageContent();

        $publishedFrom = $pageContent->getPublishedFrom();
        if (empty($publishedFrom)) {
            $publishedFrom = null;
        }

        $publishedUntil = $pageContent->getPublishedUntil();
        if (empty($publishedUntil)) {
            $publishedUntil = null;
        }

        $page->setName($pageContent->getName())
            ->setStatus($pageContent->getStatus())
            ->setPublishedFrom($publishedFrom)
            ->setPublishedUntil($publishedUntil);
    }
}
