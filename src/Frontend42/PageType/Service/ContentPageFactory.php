<?php
namespace Frontend42\PageType\Service;

use Frontend42\PageType\ContentPageType;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ContentPageFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ContentPageType(
            $serviceLocator->getServiceLocator()->get('TableGateway')->get('Frontend42\PageVersion'),
            $serviceLocator->getServiceLocator()->get('TableGateway')->get('Frontend42\Content'),
            $serviceLocator->getServiceLocator()->get('Form')
        );
    }
}
