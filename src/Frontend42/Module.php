<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42;

use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\Authentication\AuthenticationService;
use Core42\Console\Console;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\MvcEvent;

class Module implements
    ConfigProviderInterface,
    InitProviderInterface,
    BootstrapListenerInterface
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return array_merge(
            include __DIR__ . '/../../config/module.config.php',
            include __DIR__ . '/../../config/pagetypes.config.php',
            include __DIR__ . '/../../config/blocks.config.php',
            include __DIR__ . '/../../config/assets.config.php',
            include __DIR__ . '/../../config/admin.config.php',
            include __DIR__ . '/../../config/navigation.config.php',
            include __DIR__ . '/../../config/routing.config.php',
            include __DIR__ . '/../../config/cli.config.php',
            include __DIR__ . '/../../config/caches.config.php',
            include __DIR__ . '/../../config/translation.config.php'
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
        $e->getApplication()->getEventManager()->getSharedManager()->attach(
            'Zend\Mvc\Controller\AbstractController',
            MvcEvent::EVENT_DISPATCH,
            function ($e) {
                $controller      = $e->getTarget();

                if (!($controller instanceof AbstractAdminController)) {
                    return;
                }

                $sm = $e->getApplication()->getServiceManager();

                $viewHelperManager = $sm->get('viewHelperManager');

                $headScript = $viewHelperManager->get('headScript');
                $headLink = $viewHelperManager->get('headLink');
                $basePath = $viewHelperManager->get('basePath');

                $headScript->appendFile($basePath('/assets/admin/frontend/js/vendor.min.js'));
                $headScript->appendFile($basePath('/assets/admin/frontend/js/frontend42.min.js'));
                $headLink->appendStylesheet($basePath('/assets/admin/frontend/css/frontend42.min.css'));

                $formElement = $viewHelperManager->get('formElement');
                $formElement->addClass('Frontend42\FormElements\PageSelector', 'formpageselector');
            },
            100
        );

        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, array($this, 'localeSelection'));
        $e->getApplication()->getEventManager()->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            array($this, 'localeErrorSelection')
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

        $routeMatch = $e->getRouteMatch();

        //check if frontend route
        if (substr($routeMatch->getMatchedRouteName(), 0, 6) == "admin/") {
            return;
        }

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
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $manager
     * @return void
     */
    public function init(ModuleManagerInterface $manager)
    {
        $events = $manager->getEventManager();

        $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, array($this, 'onMergeConfig'));
    }

    /**
     * @param ModuleEvent $e
     */
    public function onMergeConfig(ModuleEvent $e)
    {
        $configListener = $e->getConfigListener();
        $config         = $configListener->getMergedConfig(false);

        if ($config['caches']['Cache\Sitemap']['adapter']['name'] !== 'filesystem') {
            unset($config['caches']['Cache\Sitemap']['adapter']['options']['cache_dir']);
            unset($config['caches']['Cache\Sitemap']['adapter']['options']['dirPermission']);
            unset($config['caches']['Cache\Sitemap']['adapter']['options']['filePermission']);
        }

        $configListener->setMergedConfig($config);
    }
}
