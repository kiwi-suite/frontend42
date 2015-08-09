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
        $pageMapping = [];
        $handleMapping = [];

        if ($cache->hasItem('pageMapping')) {
            $pageMapping = $cache->getItem("pageMapping");
        }
        if ($cache->hasItem('handleMapping')) {
            $handleMapping = $cache->getItem("handleMapping");
        }

        $pageHandler = new PageHandler(
            $serviceLocator->get('TableGateway')->get('Frontend42\Page'),
            $serviceLocator->get('Selector')->get('Frontend42\PageVersion')
        );
        $pageHandler->setDefaultHandle($defaultHandle);
        $pageHandler->setHandleMapping($handleMapping);
        $pageHandler->setPageMapping($pageMapping);

        return $pageHandler;
    }
}
