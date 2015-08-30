<?php
namespace Frontend42\Event\Service;

use Frontend42\Event\BlockEvent;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BlockEventManagerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $eventManager = new EventManager();
        $eventManager->setEventClass(new BlockEvent());

        return $eventManager;
    }
}
