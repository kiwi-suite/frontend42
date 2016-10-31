<?php
namespace Frontend42\PageType\Service;

use Core42\Hydrator\Mutator\Mutator;
use Frontend42\PageType\DefaultPageType;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class PageTypeAbstractFactory implements AbstractFactoryInterface
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
        /** @var PageTypePluginManager $pageTypePluginManager */
        $pageTypePluginManager = $container->get(PageTypePluginManager::class);
        $pageTypes = $pageTypePluginManager->getAvailablePageTypes();

        return in_array($requestedName, $pageTypes);
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
        $config = (isset($config['page_types']['page_types'])) ? $config['page_types']['page_types'] : [];
        $options = $config[$requestedName];

        /** @var PageTypePluginManager $pageTypePluginManager */
        $pageTypePluginManager = $container->get(PageTypePluginManager::class);
        $pageTypeClass = (isset($options['class'])) ? $options['class'] : DefaultPageType::class;

        unset($options['class']);

        $pageType = $pageTypePluginManager->build($pageTypeClass, $options);
        $pageType->setMutator($container->get(Mutator::class));

        return $pageType;
    }
}
