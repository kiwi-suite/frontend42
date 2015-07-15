<?php
namespace Frontend42\Navigation\Provider\Service;

use Frontend42\Navigation\Provider\Provider;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProviderFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sitemapSelector = $serviceLocator->get('Selector')->get('Frontend42\Sitemap');
        $defaultLocale = $serviceLocator->get('Localization')->getActiveLocale();

        return new Provider($sitemapSelector, $defaultLocale);
    }
}
