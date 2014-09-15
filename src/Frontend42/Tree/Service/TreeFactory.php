<?php
namespace Frontend42\Tree\Service;

use Frontend42\Tree\Tree;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TreeFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $treeTableGateway = $serviceLocator->get('TableGateway')->get('Frontend42\Tree');
        return new Tree($treeTableGateway);
    }
}
