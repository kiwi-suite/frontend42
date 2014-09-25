<?php
namespace Frontend42\Sitemap\Service;

use Frontend42\Sitemap\SitemapProvider;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SitemapProviderFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SitemapProvider(
            $serviceLocator->get('TableGateway')->get('Frontend42\Sitemap'),
            $serviceLocator->get('TableGateway')->get('Frontend42\Page')
        );
    }
}
