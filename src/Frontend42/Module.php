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
use Core42\Console\Console;
use Frontend42\I18n\Locale\LocaleOptions;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements
    ConfigProviderInterface,
    BootstrapListenerInterface
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return array_merge(
            include __DIR__ . '/../../config/module.config.php',
            include __DIR__ . '/../../config/locale.config.php',
            include __DIR__ . '/../../config/navigation.config.php',
            include __DIR__ . '/../../config/routing.config.php',
            include __DIR__ . '/../../config/assets.config.php',
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

                $headScript->appendFile($basePath('/assets/admin/frontend/js/raum42-frontend.min.js'));
                $headLink->appendStylesheet($basePath('/assets/admin/frontend/css/raum42-frontend.min.css'));
            }
        );

        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, array($this, 'localeSelection'));
    }

    /**
     * @param MvcEvent $e
     */
    public function localeSelection(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        /** @var LocaleOptions $localeOptions */
        $localeOptions = $serviceManager->get('Frontend42\LocaleOptions');

        \Locale::setDefault($localeOptions->getDefault());

        if (Console::isConsole()) {
            return;
        }

        $serviceManager->get('MvcTranslator')->setLocale(\Locale::getDefault());

        if (count($localeOptions->getList()) <= 1) {
            return;
        }

        $routeMatch = $e->getRouteMatch();

        if ($localeOptions->getSelection() == LocaleOptions::SELECTION_LANGUAGE) {
            $langList = array();
            foreach ($localeOptions->getList() as $_locale) {
                $langList[$_locale] = \Locale::getPrimaryLanguage($_locale);
            }
            $lang = $routeMatch->getParam("lang", null);

            if ($lang === null) {
                $httpAcceptLanguage = $serviceManager->get('request')->getServer('HTTP_ACCEPT_LANGUAGE');

                if (!empty($httpAcceptLanguage)) {
                    $requestLocale = \Locale::acceptFromHttp($httpAcceptLanguage);

                    if (in_array($requestLocale, $localeOptions->getList())) {
                        \Locale::setDefault($requestLocale);
                    } elseif (in_array(\Locale::getPrimaryLanguage($requestLocale), $langList)) {
                        $requestLocale = array_search(\Locale::getPrimaryLanguage($requestLocale), $langList);
                        \Locale::setDefault($requestLocale);
                    }
                }
            } else {
                if (in_array($lang, $langList)) {
                    $requestLocale = array_search($lang, $langList);
                    \Locale::setDefault($requestLocale);
                }
            }
        } elseif ($localeOptions->getSelection() == LocaleOptions::SELECTION_LOCALE) {
            $locale = $routeMatch->getParam("locale", null);

            if ($locale === null) {
                $httpAcceptLanguage = $serviceManager->get('request')->getServer('HTTP_ACCEPT_LANGUAGE');

                if (!empty($httpAcceptLanguage)) {
                    $requestLocale = \Locale::acceptFromHttp($httpAcceptLanguage);

                    if (in_array($requestLocale, $localeOptions->getList())) {
                        \Locale::setDefault($requestLocale);
                    }
                }
            } else {
                if (in_array($locale, $localeOptions->getList())) {
                    \Locale::setDefault($locale);
                }
            }
        }

        $serviceManager->get('MvcTranslator')->setLocale(\Locale::getDefault());
    }
}
