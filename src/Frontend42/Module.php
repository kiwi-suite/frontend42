<?php
namespace Frontend42;

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
        $e->getApplication()->getEventManager()->attach(
            MvcEvent::EVENT_ROUTE,
            function(MvcEvent $e) {
                $serviceManager = $e->getApplication()->getServiceManager();

                /** @var LocaleOptions $localeOptions */
                $localeOptions = $serviceManager->get('Frontend42\LocaleOptions');

                \Locale::setDefault($localeOptions->getDefault());

                if (count($localeOptions->getList()) <= 1) {
                    return;
                }

                if (Console::isConsole()) {
                    return;
                }

                $routeMatch = $e->getRouteMatch();

                $lang = $routeMatch->getParam("lang", null);

                if ($lang === null) {
                    $httpAcceptLanguage = $serviceManager->get('request')->getServer('HTTP_ACCEPT_LANGUAGE');

                    if (!empty($httpAcceptLanguage)) {
                        $requestLocale = \Locale::acceptFromHttp($httpAcceptLanguage);

                        if (in_array($requestLocale, $localeOptions->getList())) {
                            \Locale::setDefault($requestLocale);
                        } elseif (array_key_exists(\Locale::getPrimaryLanguage($requestLocale), $localeOptions->getList())) {
                            $localeList = $localeOptions->getList();
                            $requestLocale = $localeList[\Locale::getPrimaryLanguage($requestLocale)];
                            \Locale::setDefault($requestLocale);
                        }
                    }
                } else {
                    if (array_key_exists($lang, $localeOptions->getList())) {
                        $localeList = $localeOptions->getList();
                        $requestLocale = $localeList[\Locale::getPrimaryLanguage($lang)];
                        \Locale::setDefault($requestLocale);
                    }
                }

                $serviceManager->get('MvcTranslator')->setLocale(\Locale::getDefault());
            }
        );
    }
}
