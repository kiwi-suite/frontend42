<?php
namespace Frontend42\View\Helper\Service;

use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\BlockInheritanceTableGateway;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\View\Helper\Block;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class BlockFactory implements FactoryInterface
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
        return new Block(
            $container->get('TableGateway')->get(BlockInheritanceTableGateway::class),
            $container->get('Selector')->get(PageVersionSelector::class),
            $container->get('TableGateway')->get(PageTableGateway::class),
            $container->get('Cache\Block')
        );
    }
}
