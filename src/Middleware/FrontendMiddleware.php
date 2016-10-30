<?php
namespace Frontend42\Middleware;

use Core42\I18n\Localization\Localization;
use Core42\Stdlib\DefaultGetterTrait;
use Frontend42\Selector\PageSelector;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\MvcEvent;
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

        $pageId = $mvcEvent->getRouteMatch()->getParam("pageId");
        if (empty($pageId)) {
            throw new \Exception("no pageId set inside frontend route");
        }

        $page = $this->getSelector(PageSelector::class)->setPageId($pageId)->getResult();
        if (empty($page)) {
            throw new \Exception("no pageId set inside frontend route");
        }

        $localization = $this->getServiceManager()->get(Localization::class);
        $localization->acceptLocale($page->getLocale());
        $this->getServiceManager()->get(TranslatorInterface::class)->setLocale($localization->getActiveLocale());
    }
}
