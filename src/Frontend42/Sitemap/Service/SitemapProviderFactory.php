<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

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
