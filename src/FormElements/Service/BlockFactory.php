<?php
namespace Frontend42\FormElements\Service;

use Frontend42\FormElements\Block;
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
        if ($options === null) {
            $options = [];
        }

        $name = null;
        if (isset($options['name'])) {
            $name = $options['name'];
        }

        if (isset($options['options'])) {
            $options = $options['options'];
        }

        $blockConfig = $container->get('config')['blocks']['blocks'];
        return new Block($name, $options, $blockConfig);
    }
}
