<?php
namespace Frontend42\Page\Service;

use Frontend42\Command\Frontend\BuildIndexCommand;
use Frontend42\Page\Data\Adapter\CacheAdapter;
use Frontend42\Page\Data\Adapter\DatabaseAdapter;
use Frontend42\Page\Data\Adapter\MemoryAdapter;
use Frontend42\Page\Data\Data;
use Frontend42\Page\Data\Storage\CacheStorage;
use Frontend42\Page\Data\Storage\MemoryStorage;
use Frontend42\PageType\Provider\PageTypeProvider;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class DataFactory implements FactoryInterface
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
        $data = new Data();
        $data->addAdapter(new MemoryAdapter(
            $container->get('Frontend42\Page\MemoryData')
        ));
        $data->addAdapter(new CacheAdapter(
            $container->get('Cache')->get('frontend'),
            $container->get('Cache')->get('sitemap'),
            $container->get('Cache')->get('page'),
            $container->get('Cache')->get('pageversion')
        ));

        $data->addAdapter(new DatabaseAdapter(
            $container->get('TableGateway')->get(PageTableGateway::class),
            $container->get('TableGateway')->get(SitemapTableGateway::class),
            $container->get('Selector')->get(PageVersionSelector::class),
            $container->get('Command')->get(BuildIndexCommand::class),
            $container->get(PageTypeProvider::class)
        ));

        $data->addStorage(new MemoryStorage(
            $container->get('Frontend42\Page\MemoryData')
        ));
        $data->addStorage(new CacheStorage(
            $container->get('Cache')->get('frontend'),
            $container->get('Cache')->get('sitemap'),
            $container->get('Cache')->get('page'),
            $container->get('Cache')->get('pageversion')
        ));

        return $data;
    }
}
