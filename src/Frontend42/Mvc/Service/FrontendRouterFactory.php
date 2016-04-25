<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Mvc\Service;

use Frontend42\Model\Page;
use Frontend42\Selector\SitemapSelector;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FrontendRouterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $routerConfig = [
            'routes' => [],
        ];

        $sm = $serviceLocator->getServiceLocator();
        $locales = $sm->get('Localization')->getLocales();
        //$config = $serviceLocator->get('Config');

        /* @var SitemapSelector $sitemapSelector */
        $sitemapSelector = $sm->get('Selector')->get('Frontend42\Sitemap');

        foreach ($locales as $locale) {

            $sitemapSelector->setLocale($locale);
            $sitemapResult = $sitemapSelector->getResult();

            foreach ($sitemapResult as $sitemap) {
                $this->buildRoutes($sitemap);
            }
        }

        $routerConfig['routes']['frontend'] = [
            'type' => 'Zend\Mvc\Router\Http\Literal',
            'options' => [
                'route' => '',
                'defaults' => [
                    'controller' => __NAMESPACE__ . '\Sitemap',
                    'action' => 'index',
                ],
            ],
        ];

        $routePluginManager = $sm->get('RoutePluginManager');
        $routerConfig['route_plugins'] = $routePluginManager;


        return TreeRouteStack::factory($routerConfig);
    }

    /**
     * @param $sitemap
     */
    protected function buildRoutes($sitemap)
    {
        /* @var Page $page*/
        $page = $sitemap['page'];
        $pageRoute = json_decode($page->getRoute(), true);

        if (count($sitemap['children']) > 0) {
            foreach ($sitemap['children'] as $child) {
                $this->buildRoutes($child);
            }
        }
    }
}
