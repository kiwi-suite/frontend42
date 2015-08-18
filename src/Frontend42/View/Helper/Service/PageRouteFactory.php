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
        return new PageRoute(
            $serviceLocator->getServiceLocator()->get('Frontend42\Navigation\PageHandler')
        );
    }
}
