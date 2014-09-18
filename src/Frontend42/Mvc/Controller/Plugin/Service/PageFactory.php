<?php
namespace Frontend42\Mvc\Controller\Plugin\Service;

use Frontend42\Mvc\Controller\Plugin\Page;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PageFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Page(
            $serviceLocator->getServiceLocator()->get('Core42\Navigation')->getContainer('frontend42'),
            $serviceLocator->getServiceLocator()->get('Core42\Navigation')
        );
    }
}
