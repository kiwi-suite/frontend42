<?php
namespace Frontend42\Middleware;

use Core42\I18n\Localization\Localization;
use Core42\Stdlib\DefaultGetterTrait;
use Frontend42\Page\Page;
use Frontend42\Selector\PageVersionSelector;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

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
     */
    public function __invoke(Request $request, Response $response)
    {
        /** @var MvcEvent $mvcEvent */
        $mvcEvent = $this->getServiceManager()->get('Application')->getMvcEvent();

        $pageId = $mvcEvent->getRouteMatch()->getParam("pageId");
        if (empty($pageId)) {
            //TODO Error
            return;
        }

        /** @var Page $page */
        $page = $this->getServiceManager()->get(Page::class);
        $page->initialize($pageId, PageVersionSelector::VERSION_APPROVED);

        /** @var Localization $localization */
        $localization = $this->getServiceManager()->get(Localization::class);

        $locale = $mvcEvent->getRouteMatch()->getParam("locale", $localization->getDefaultLocale());
        $localization->acceptLocale($locale);
        $this->getServiceManager()->get(TranslatorInterface::class)->setLocale($localization->getActiveLocale());
    }
}
