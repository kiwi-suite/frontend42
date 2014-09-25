<?php
namespace Frontend42\Navigation\Provider\Service;

use Frontend42\Navigation\Provider\DatabaseProvider;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DatabaseProviderFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DatabaseProvider(
            $serviceLocator->get('Frontend42\SitemapProvider'),
            $serviceLocator->get('TableGateway')->get('Frontend42\Page')
        );
    }
}
