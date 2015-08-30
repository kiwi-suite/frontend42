<?php
namespace Frontend42\Navigation\Service;

use Frontend42\Navigation\PageHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PageHandlerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $defaultHandle = $serviceLocator->get('config')['page_types']['default_handle'];

        $cache = $serviceLocator->get('Cache\Sitemap');

        if (!$cache->hasItem('sitemapMapping')){
            $serviceLocator->get('Command')->get('Frontend42\Navigation\CreateFrontendNavigation')->run();
        }

        $pageHandler = new PageHandler(
            $serviceLocator->get('TableGateway')->get('Frontend42\Page'),
            $serviceLocator->get('Selector')->get('Frontend42\PageVersion'),
            $cache
        );
        $pageHandler->setDefaultHandle($defaultHandle);

        return $pageHandler;
    }
}
