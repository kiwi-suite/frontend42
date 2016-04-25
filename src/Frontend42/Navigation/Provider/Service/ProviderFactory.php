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
        $defaultLocale = $serviceLocator->get('Localization')->getActiveLocale();

        $cache = $serviceLocator->get('Cache\Sitemap');

        if (!$cache->hasItem('nav_' . $defaultLocale)) {
            $serviceLocator->get('Command')->get('Frontend42\Navigation\CreateFrontendNavigation')->run();
        }

        $pages = [];
        if ($cache->hasItem('nav_' . $defaultLocale)) {
            $pages = $cache->getItem('nav_' . $defaultLocale);
        }

        return new Provider($pages, $defaultLocale);
    }
}
