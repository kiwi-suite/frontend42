<?php
namespace Frontend42\Mvc\Router;

use Zend\Mvc\Router\Exception\InvalidArgumentException;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Stdlib\ArrayUtils;

class FrontendRouter extends TreeRouteStack
{

    public static function factory($options = [])
    {
        if ($options instanceof \Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        $serviceManager = $options['route_plugins']->getServiceLocator();
        $cache = $serviceManager->get('Cache\Sitemap');
        if ($cache->hasItem('sitemap')) {
            $serviceManager->get('Command')->get('Frontend42\Router\CreateRouteConfig')->run();
        }
        $frontendRoutes = $cache->getItem("sitemap");
        $frontendRoutes = (empty($frontendRoutes)) ? [] : $frontendRoutes;
        $options['routes']['frontend']['child_routes'] = $frontendRoutes;

        return parent::factory($options);
    }
}
