<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Mvc\Router\Http;

use Frontend42\Sitemap\SitemapProvider;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack;

class Database extends TranslatorAwareTreeRouteStack
{
    /**
     * @param array $options
     * @return \Zend\Mvc\Router\SimpleRouteStack
     */
    public static function factory($options = array())
    {
        $serviceManager = $options['route_plugins']->getServiceLocator();

        /** @var SitemapProvider $sitemapProvider */
        $sitemapProvider = $serviceManager->get('Frontend42\SitemapProvider');
        $tree = $sitemapProvider->getTree();

        $routes = self::parseTree($tree);

        $options['routes'] = array_merge($options['routes'], $routes);
        $router = parent::factory($options);

        if ($router instanceof TranslatorAwareInterface) {
            $router->setTranslator($serviceManager->get('MvcTranslator'));
            $router->setTranslatorTextDomain("router");
        }

        return $router;
    }

    /**
     * @param array $tree
     * @return array
     */
    protected static function parseTree($tree)
    {
        $routes = array();

        foreach ($tree as $_tree) {
            /** @var \Frontend42\Model\Sitemap $sitemap */
            $sitemap = $_tree['model'];

            $key = 'page_' . $sitemap->getId();

            $defaults = json_decode($sitemap->getDefaultParams(), true);
            $defaults['sitemapId'] =  $sitemap->getId();

            $routes[$key] = array(
                'type' => $sitemap->getRouteClass(),
                'options' => array(
                    'route' => $sitemap->getRoute(),
                    'defaults' => $defaults,
                ),
            );

            if ($sitemap->getRouteConstraints() !== null) {
                $routes[$key]['options']['constraints'] = json_decode($sitemap->getRouteConstraints(), true);
            }

            if (!empty($_tree['children'])) {
                $routes[$key]['may_terminate'] = true;
                $routes[$key]['child_routes'] = self::parseTree($_tree['children']);
            }
        }

        return $routes;
    }
}
