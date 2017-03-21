<?php
namespace Frontend42\View\Helper\Service;

use Frontend42\Router\PageRoute;
use Frontend42\Selector\PageSelector;
use Frontend42\Selector\SitemapSelector;
use Frontend42\View\Helper\SwitchLanguage;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class SwitchLanguageFactory implements FactoryInterface
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
        return new SwitchLanguage(
            $container->get('Selector')->get(PageSelector::class),
            $container->get(PageRoute::class),
            $container->get('Selector')->get(SitemapSelector::class)
        );
    }
}
