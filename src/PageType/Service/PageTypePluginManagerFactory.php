<?php
namespace Frontend42\PageType\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class PageTypePluginManagerFactory implements FactoryInterface
{

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
        $pageTypes = $container->get('config')['page_types']['page_types'];
        if (!empty($pageTypes)){
            $pageTypes = array_keys($pageTypes);
        }

        return new PageTypePluginManager(
            $container,
            $pageTypes,
            $container->get('config')['page_types']['service_manager']
        );
    }
}
