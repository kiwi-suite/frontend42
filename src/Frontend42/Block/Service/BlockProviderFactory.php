<?php
namespace Frontend42\Block\Service;

use Frontend42\Block\BlockProvider;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BlockProviderFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config')['blocks'];

        $blockProvider = new BlockProvider();
        $blockProvider->loadBlockTypes($config['paths']);
        $blockProvider->setFormElementManager($serviceLocator->get("FormElementManager"));

        return $blockProvider;
    }
}
