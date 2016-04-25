<?php
namespace Frontend42\Link\Adapter\Service;

use Frontend42\Link\Adapter\SitemapLink;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SitemapLinkFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SitemapLink(
            $serviceLocator->get('TableGateway')->get('Frontend42\Page'),
            $serviceLocator->get('Router'),
            $serviceLocator->get('Frontend42\Navigation\PageHandler')
        );
    }
}
