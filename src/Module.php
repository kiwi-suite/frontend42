<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42;

use Admin42\ModuleManager\Feature\AdminAwareModuleInterface;
use Admin42\ModuleManager\GetAdminConfigTrait;
use Core42\Console\Console;
use Core42\I18n\Localization\Localization;
use Core42\ModuleManager\GetConfigTrait;
use Core42\Mvc\Environment\Environment;
use Frontend42\Event\PageEventListener;
use Frontend42\FormElements\Block;
use Frontend42\FormElements\PageSelector;
use Frontend42\FormElements\PageTypeSelector;
use Frontend42\FormElements\Service\BlockFactory;
use Frontend42\FormElements\Service\PageSelectorFactory;
use Frontend42\FormElements\Service\PageTypeSelectorFactory;
use Zend\EventManager\EventInterface;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements
    ConfigProviderInterface,
    BootstrapListenerInterface,
    AdminAwareModuleInterface
{
    use GetConfigTrait;
    use GetAdminConfigTrait;

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        /*$e->getApplication()->getEventManager()->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'localeSelection']
        );
        $e->getApplication()->getEventManager()->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'localeErrorSelection']
        );*/

        $e->getApplication()
            ->getServiceManager()
            ->get(PageEventListener::class)
            ->attach($e->getApplication()->getServiceManager()->get('Frontend42\Page\EventManager'));
    }

    /**
     * @param MvcEvent $e
     */
    public function localeErrorSelection(MvcEvent $e)
    {
        if (Console::isConsole()) {
            return;
        }

        $serviceManager = $e->getApplication()->getServiceManager();

        if ($serviceManager->get(Environment::class)->is(\Admin42\Module::class)) {
            return;
        }

        $localization = $serviceManager->get('Localization');
        $locale = $localization->getDefaultLocale();
        $localization->acceptLocale($locale);
        $serviceManager->get(TranslatorInterface::class)->setLocale($locale);
    }

    /**
     * @param MvcEvent $e
     */
    public function localeSelection(MvcEvent $e)
    {
        if (Console::isConsole()) {
            return;
        }

        $serviceManager = $e->getApplication()->getServiceManager();

        if ($serviceManager->get(Environment::class)->is(\Admin42\Module::ENVIRONMENT_ADMIN)) {
            return;
        }

        $routeMatch = $e->getRouteMatch();
        $localization = $serviceManager->get(Localization::class);

        //error page or not mapped page
        if (!($routeMatch->getParam("pageId", null) > 0)) {
            $locale = $localization->getDefaultLocale();
            $localization->acceptLocale($locale);
            $serviceManager->get(TranslatorInterface::class)->setLocale($locale);
            return;
        }

        $locale = $routeMatch->getParam("locale", $localization->getLocaleFromHeader());
        $localization->acceptLocale($locale);
        $serviceManager->get(TranslatorInterface::class)->setLocale($locale);

        $pageHandler = $serviceManager->get(PageHandler::class);
        $pageHandler->loadCurrentPage($routeMatch->getParam("pageId"));
    }
}
