<?php
namespace Frontend42\View\Helper\Service;

use Frontend42\View\Helper\Block;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BlockFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Block(
            $serviceLocator->getServiceLocator()->get('TableGateway')->get('Frontend42\BlockInheritance'),
            $serviceLocator->getServiceLocator()->get('Selector')->get('Frontend42\PageVersion'),
            $serviceLocator->getServiceLocator()->get('TableGateway')->get('Frontend42\Page'),
            $serviceLocator->getServiceLocator()->get('Cache\Block')
        );
    }
}
