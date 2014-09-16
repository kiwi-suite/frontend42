<?php
namespace Frontend42\Mvc\Router\Http;

use Frontend42\I18n\Locale\LocaleOptions;
use Frontend42\Tree\Tree;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack;

class Database extends TranslatorAwareTreeRouteStack
{
    public static function factory($options = array())
    {
        $serviceManager = $options['route_plugins']->getServiceLocator();

        /** @var Tree $treeReceiver */
        $treeReceiver = $serviceManager->get('Frontend42\Tree');
        $tree = $treeReceiver->getTree();

        $localeOptions = $serviceManager->get('Frontend42\LocaleOptions');
        $routes = self::parseTree($tree, $localeOptions);

        $options['routes'] = array_merge($options['routes'], $routes);
        $router = parent::factory($options);

        if ($router instanceof TranslatorAwareInterface) {
            $router->setTranslator($serviceManager->get('MvcTranslator'));
            $router->setTranslatorTextDomain("router");
        }

        return $router;
    }

    protected static function parseTree($tree, LocaleOptions $localeOptions)
    {
        $routes = array();

        foreach ($tree as $_tree) {
            /** @var \Frontend42\Model\Tree $treeModel */
            $treeModel = $_tree['model'];

            $key = 'page_' . $treeModel->getId();

            $routes[$key] = array(
                'type' => 'segment',
                'options' => array(
                    'route' => '{slug_'.$treeModel->getId().'}/',
                    'defaults' => array(
                        'controller' => $treeModel->getController(),
                        'action' => $treeModel->getAction()
                    ),
                ),
            );

            if ($treeModel->getRoot() === true) {
                $rootRoute = (count($localeOptions->getList()) > 0) ? '/:lang/' : '/';
                $routes[$key]['options']['route'] = $rootRoute;

                if ((count($localeOptions->getList()) > 0)) {
                    $languages = $localeOptions->getList();
                    $languages = array_keys($languages);

                    $routes[$key]['options']['constraints']['lang'] = '('.implode('|',$languages).')?';
                }
            }

            if (!empty($_tree['children'])) {
                $routes[$key]['may_terminate'] = true;
                $routes[$key]['child_routes'] = self::parseTree($_tree['children'], $localeOptions);
            }
        }

        return $routes;
    }
}
