<?php
namespace Frontend42\Event;

use Core42\Stdlib\DefaultGetterTrait;
use Frontend42\Command\Navigation\SaveNavigationCommand;
use Frontend42\Model\Page;
use Frontend42\Selector\ApprovedPageContentSelector;
use Frontend42\Selector\PageSelector;
use Frontend42\Selector\RoutingSelector;
use Frontend42\Selector\SitemapSelector;
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
        $events->attach(PageEvent::EVENT_EDIT_PRE, [$this, 'setStandardParams']);
        $events->attach(PageEvent::EVENT_APPROVED, [$this, 'setStandardParams']);

        $events->attach(PageEvent::EVENT_ADD_PRE, [$this, 'setSlug']);
        $events->attach(PageEvent::EVENT_EDIT_PRE, [$this, 'setSlug']);
        $events->attach(PageEvent::EVENT_APPROVED, [$this, 'setSlug']);

        $events->attach(PageEvent::EVENT_ADD_POST, [$this, 'saveNavigation']);
        $events->attach(PageEvent::EVENT_EDIT_POST, [$this, 'saveNavigation']);
        $events->attach(PageEvent::EVENT_APPROVED, [$this, 'saveNavigation']);

        $events->attach(PageEvent::EVENT_APPROVED, [$this, 'updateCache']);
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

    public function saveNavigation(PageEvent $event)
    {
        if ($event->getApproved() === false) {
            return;
        }

        $pageContent = $event->getPageContent();

        if (!in_array("navigation", $pageContent->getProperties())) {
            return;
        }

        $navs = $pageContent->getNavigation();
        if (!is_array($navs) || empty($navs)) {
            $navs = [];
        }


        $this
            ->getCommand(SaveNavigationCommand::class)
            ->setPageId($event->getTarget()->getId())
            ->setNavs($navs)
            ->run();
    }

    public function updateCache(PageEvent $event)
    {
        if ($event->getApproved() === false) {
            return;
        }

        /** @var Page $page */
        $page = $event->getTarget();
        $this->getSelector(PageSelector::class)
            ->setPageId($page->getId())
            ->setDisableCache(true)
            ->getResult();

        $this->getSelector(SitemapSelector::class)
            ->setSitemapId($page->getSitemapId())
            ->setDisableCache(true)
            ->getResult();

        $this->getSelector(ApprovedPageContentSelector::class)
            ->setPageId($page->getId())
            ->setDisableCache(true)
            ->getResult();

        $this->getSelector(RoutingSelector::class)
            ->setDisableCache(true)
            ->getResult();
    }
}
