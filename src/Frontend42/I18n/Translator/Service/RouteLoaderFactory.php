<?php
namespace Frontend42\I18n\Translator\Service;

use Frontend42\I18n\Translator\Loader\Router;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RouteLoaderFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Router(
            $serviceLocator->getServiceLocator()->get('TableGateway')->get('Frontend42\Page')
        );
    }
}
