<?php
namespace Frontend42\View\Helper\Service;

use Frontend42\View\Helper\PageRoute;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PageRouteFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $defaultHandle = $serviceLocator->getServiceLocator()->get('config')['page_types']['default_handle'];

        $cache = $serviceLocator->getServiceLocator()->get('Cache\Sitemap');
        $pageMapping = [];
        $handleMapping = [];

        if ($cache->hasItem('pageMapping')) {
            $pageMapping = $cache->getItem("pageMapping");
        }
        if ($cache->hasItem('handleMapping')) {
            $handleMapping = $cache->getItem("handleMapping");
        }

        return new PageRoute($pageMapping, $handleMapping, $defaultHandle);
    }
}
