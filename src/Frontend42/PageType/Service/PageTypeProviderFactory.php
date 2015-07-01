<?php
namespace Frontend42\PageType\Service;

use Frontend42\PageType\PageTypeProvider;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PageTypeProviderFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config')['page_types'];

        $pageTypeProvider = new PageTypeProvider(new Config($config['service_manager']));
        $pageTypeProvider->loadPageTypes($config['paths']);

        return $pageTypeProvider;
    }
}
