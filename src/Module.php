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
use Core42\Authentication\AuthenticationService;
use Core42\Console\Console;
use Core42\Mvc\Environment\Environment;
use Frontend42\FormElements\Block;
use Frontend42\FormElements\PageSelector;
use Frontend42\FormElements\PageTypeSelector;
use Frontend42\FormElements\Service\BlockFactory;
use Frontend42\FormElements\Service\PageSelectorFactory;
use Frontend42\FormElements\Service\PageTypeSelectorFactory;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements
    ConfigProviderInterface,
    BootstrapListenerInterface,
    AdminAwareModuleInterface
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return array_merge(
            include __DIR__ . '/../config/module.config.php',
            include __DIR__ . '/../config/pagetypes.config.php',
            include __DIR__ . '/../config/blocks.config.php',
            include __DIR__ . '/../config/assets.config.php',
            include __DIR__ . '/../config/admin.config.php',
            include __DIR__ . '/../config/navigation.config.php',
            include __DIR__ . '/../config/routing.config.php',
            include __DIR__ . '/../config/cli.config.php',
            include __DIR__ . '/../config/caches.config.php',
            include __DIR__ . '/../config/permission.config.php',
            include __DIR__ . '/../config/translation.config.php'
        );
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $e->getApplication()->getEventManager()->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'localeSelection']
        );
        $e->getApplication()->getEventManager()->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'localeErrorSelection']
        );
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
        $serviceManager->get('MvcTranslator')->setLocale($locale);
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

        if ($serviceManager->get(Environment::class)->is(\Admin42\Module::class)) {
            return;
        }

        $routeMatch = $e->getRouteMatch();
        $localization = $serviceManager->get('Localization');

        //error page or not mapped page
        if (!($routeMatch->getParam("pageId", null) > 0)) {
            $locale = $localization->getDefaultLocale();
            $localization->acceptLocale($locale);
            $serviceManager->get('MvcTranslator')->setLocale($locale);
            return;
        }

        $locale = $routeMatch->getParam("locale", $localization->getLocaleFromHeader());
        $localization->acceptLocale($locale);
        $serviceManager->get('MvcTranslator')->setLocale($locale);

        $versionId = $e->getRequest()->getQuery('versionId');

        $pageHandler = $serviceManager->get('Frontend42\Navigation\PageHandler');
        if ($versionId !== null) {
            /* @var AuthenticationService $authenticationService */
            $authenticationService = $serviceManager->get('Admin42\Authentication');
            if ($authenticationService->hasIdentity()) {
                $pageHandler->loadCurrentPage($routeMatch->getParam("pageId"), $versionId);
                return;
            }
        }

        $pageHandler->loadCurrentPage($routeMatch->getParam("pageId"));
    }

    /**
     * @return array
     */
    public function getAdminStylesheets()
    {
        return [
            '/assets/admin/frontend/css/frontend42.min.css'
        ];
    }

    /**
     * @return array
     */
    public function getAdminJavascript()
    {
        return [
            '/assets/admin/frontend/js/vendor.min.js',
            '/assets/admin/frontend/js/frontend42.min.js'
        ];
    }

    /**
     * @return array
     */
    public function getAdminViewHelpers()
    {
        return [

        ];
    }

    /**
     * @return array
     */
    public function getAdminFormElements()
    {
        return [
            'factories' => [
                PageTypeSelector::class     => PageTypeSelectorFactory::class,
                PageSelector::class         => PageSelectorFactory::class,
                Block::class                => BlockFactory::class,
            ],
            'aliases' => [
                'page_type_selector'        => PageTypeSelector::class,
                'page_selector'             => PageSelector::class,
                'block'                     => Block::class,
            ],
        ];
    }
}
