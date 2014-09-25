<?php
namespace Frontend42\Form\Service;

use Frontend42\Form\PageAddForm;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PageAddFormFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->getServiceLocator()->get('config');
        $config = $config['page_types'];

        $form = new PageAddForm(
            $serviceLocator->getServiceLocator()->get('Frontend42\SitemapProvider'),
            $config
        );
        $form->init();

        return $form;
    }
}
