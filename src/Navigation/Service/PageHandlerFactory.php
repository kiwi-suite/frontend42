<?php
namespace Frontend42\Navigation\Service;

use Frontend42\Command\Navigation\CreateFrontendNavigationCommand;
use Frontend42\Navigation\PageHandler;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\PageVersionTableGateway;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class PageHandlerFactory implements FactoryInterface
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
        $defaultHandle = $container->get('config')['page_types']['default_handle'];

        $cache = $container->get('Cache\Sitemap');

        if (!$cache->hasItem('sitemapMapping')) {
            $container->get('Command')->get(CreateFrontendNavigationCommand::class)->run();
        }

        $pageHandler = new PageHandler(
            $container->get('TableGateway')->get(PageTableGateway::class),
            $container->get('Selector')->get(PageVersionTableGateway::class),
            $cache
        );
        $pageHandler->setDefaultHandle($defaultHandle);

        return $pageHandler;
    }
}
