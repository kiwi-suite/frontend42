<?php
namespace Frontend42\Block\Service;

use Frontend42\Block\DefaultBlock;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class BlockAbstractFactory implements AbstractFactoryInterface
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
        /** @var BlockPluginManager $blockPluginManager */
        $blockPluginManager = $container->get(BlockPluginManager::class);
        $blocks = $blockPluginManager->getAvailableBlocks();

        return in_array($requestedName, $blocks);
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
        $config = $container->get('Config');
        $config = (isset($config['blocks']['blocks'])) ? $config['blocks']['blocks'] : [];
        $options = $config[$requestedName];

        /** @var BlockPluginManager $blockPluginManager */
        $blockPluginManager = $container->get(BlockPluginManager::class);
        $blockClass = (isset($options['class'])) ? $options['class'] : DefaultBlock::class;

        unset($options['class']);

        $block = $blockPluginManager->build($blockClass, $options);

        return $block;
    }
}
