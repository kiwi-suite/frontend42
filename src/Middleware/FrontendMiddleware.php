<?php
namespace Frontend42\Middleware;

use Core42\I18n\Localization\Localization;
use Core42\Stdlib\DefaultGetterTrait;
use Frontend42\Model\Page;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\Selector\ApprovedPageContentSelector;
use Frontend42\Selector\PageSelector;
use Frontend42\Selector\SitemapSelector;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\MvcEvent;
use Zend\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FrontendMiddleware
{
    use DefaultGetterTrait;

    /**
     * FrontendMiddleware constructor.
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return null
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response)
    {
        /** @var MvcEvent $mvcEvent */
        $mvcEvent = $this->getServiceManager()->get('Application')->getMvcEvent();

        /** @var RouteMatch $routeMatch */
        $routeMatch = $mvcEvent->getRouteMatch();

        $pageId = $routeMatch->getParam("pageId");
        if (empty($pageId)) {
            throw new \Exception("no pageId set inside frontend route");
        }

        /** @var Page $page */
        $page = $this->getSelector(PageSelector::class)->setPageId($pageId)->getResult();
        if (empty($page)) {
            throw new \Exception("no pageId set inside frontend route");
        }

        $routeMatch = $this->getServiceManager()->get('Application')->getMvcEvent()->getRouteMatch();
        if ($page->getStatus() == Page::STATUS_OFFLINE) {
            $routeMatch->setParam('action', 'not-found');

            return;
        }
        if ($page->getPublishedFrom() instanceof \DateTime && $page->getPublishedFrom()->getTimestamp() > time()) {
            $routeMatch->setParam('action', 'not-found');

            return;
        }
        if ($page->getPublishedUntil() instanceof \DateTime && $page->getPublishedUntil()->getTimestamp() < time()) {
            $routeMatch->setParam('action', 'not-found');

            return;
        }


        $localization = $this->getServiceManager()->get(Localization::class);
        $localization->acceptLocale($page->getLocale());
        $this->getServiceManager()->get(TranslatorInterface::class)->setLocale($localization->getActiveLocale());

        $sitemap = $this->getSelector(SitemapSelector::class)->setSitemapId($page->getSitemapId())->getResult();

        $routeMatch->setParam("__page__", $page);
        $routeMatch->setParam("__sitemap__", $sitemap);

        $pageContent = $this->getSelector(ApprovedPageContentSelector::class)->setPageId($page->getId())->getResult();
        $pageContent = $this
            ->getServiceManager()
            ->get(PageTypePluginManager::class)
            ->get($sitemap->getPageType())
            ->mutate($pageContent);

        $routeMatch->setParam('__pageContent__', $pageContent);
    }
}
