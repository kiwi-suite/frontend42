<?php
namespace Frontend42\Mvc\Router\Http;

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

        $routes = self::parseTree($tree);

        $options['routes'] = array_merge($options['routes'], $routes);
        $router = parent::factory($options);

        if ($router instanceof TranslatorAwareInterface) {
            $router->setTranslator($serviceManager->get('MvcTranslator'));
            $router->setTranslatorTextDomain("router");
        }

        return $router;
    }

    protected static function parseTree($tree)
    {
        $routes = array();

        foreach ($tree as $_tree) {
            /** @var \Frontend42\Model\Tree $treeModel */
            $treeModel = $_tree['model'];

            $key = 'page_' . $treeModel->getId();

            $defaults = json_decode($treeModel->getDefaultParams(), true);
            $defaults['pageId'] =  $treeModel->getId();

            $routes[$key] = array(
                'type' => $treeModel->getRouteClass(),
                'options' => array(
                    'route' => $treeModel->getRoute(),
                    'defaults' => $defaults,
                ),
            );

            if (!empty($_tree['children'])) {
                $routes[$key]['may_terminate'] = true;
                $routes[$key]['child_routes'] = self::parseTree($_tree['children']);
            }
        }

        return $routes;
    }
}
