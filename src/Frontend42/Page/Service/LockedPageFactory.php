<?php
namespace Frontend42\Page\Service;

use Frontend42\Page\LockedPage;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LockedPageFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new LockedPage(
            $serviceLocator->get('TableGateway')->get('Frontend42\Tree'),
            $serviceLocator->get('TableGateway')->get('Frontend42\TreeLanguage')
        );
    }
}
