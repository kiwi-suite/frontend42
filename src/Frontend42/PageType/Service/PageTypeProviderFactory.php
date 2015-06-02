<?php
namespace Frontend42\PageType\Service;

use Frontend42\PageType\PageTypeProvider;
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
        $config = $serviceLocator->get('config');
        $config = (array_key_exists('page_types', $config)) ? $config['page_types'] : [];

        return new PageTypeProvider($config);
    }
}
