<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\FormElements\Service;

use Frontend42\FormElements\PageTypeSelector;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PageTypeSelectorFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $pageTypeProvider = $serviceLocator->getServiceLocator()->get('Frontend42\PageTypeProvider');
        $element = new PageTypeSelector();
        $element->setValueOptions($pageTypeProvider->getDisplayPageTypes());

        return $element;
    }
}
