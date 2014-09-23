<?php
namespace Frontend42\Page\Service;

use Frontend42\Page\ContentPage;
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
        return new ContentPage(
            $serviceLocator->get('TableGateway')->get('Frontend42\Tree'),
            $serviceLocator->get('TableGateway')->get('Frontend42\TreeLanguage'),
            $serviceLocator->get('TableGateway')->get('Frontend42\Content'),
            $serviceLocator->get('Form')->get('Frontend42\Content')
        );
    }
}
