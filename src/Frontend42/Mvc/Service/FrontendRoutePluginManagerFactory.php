<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Mvc\Service;

use Zend\Mvc\Service\RoutePluginManagerFactory;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class FrontendRoutePluginManagerFactory extends RoutePluginManagerFactory
{

    /**
     * Create and return a plugin manager.
     * Classes that extend this should provide a valid class for
     * the PLUGIN_MANGER_CLASS constant.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return AbstractPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var AbstractPluginManager $plugin */
        $plugin = parent::createService($serviceLocator);

        $plugin->setFactory('Frontend42\FrontendRouter', 'Frontend42\Mvc\Service\FrontendRouterFactory');

        return $plugin;
    }

}