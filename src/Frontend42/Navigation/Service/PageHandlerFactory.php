<?php
namespace Frontend42\Navigation\Service;

use Frontend42\Navigation\PageHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PageHandlerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PageHandler(
            $serviceLocator->get('TableGateway')->get('Frontend42\Page'),
            $serviceLocator->get('Selector')->get('Frontend42\PageVersion')
        );
    }
}
