<?php
/**
 * core42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\PageType\Provider\Service;

use Frontend42\PageType\Provider\PageTypeConfigProvider;
use Frontend42\PageType\Provider\PageTypeProvider;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\Stdlib\AbstractOptions;

class PageTypeFallbackAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $pageTypeOption = $this->getPageTypeOptions($container, $requestedName);
        return ($pageTypeOption !== false);
    }

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $pageTypeOption = $this->getPageTypeOptions($container, $requestedName);
        $pageType = $container->get(PageTypeProvider::class)->build($pageTypeOption['class']);
        if (!($pageType instanceof AbstractOptions)) {
            return $pageType;
        }
        unset($pageTypeOption['class']);
        $pageType->setFromArray($pageTypeOption);
        return $pageType;
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool|array
     */
    protected function getPageTypeOptions(ContainerInterface $container, $requestedName)
    {
        $pageTypeOptions = $container->get(PageTypeConfigProvider::class)->getPageTypeOptions();
        if (isset($pageTypeOptions[$requestedName])) {
            return $pageTypeOptions[$requestedName];
        }

        return false;
    }
}
