<?php
namespace Frontend42\View\Helper\Service;

use Frontend42\View\Helper\Locale;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LocaleFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Locale(
            $serviceLocator->getServiceLocator()->get('Frontend42\LocaleOptions')
        );
    }
}
