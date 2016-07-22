<?php
namespace Frontend42\Navigation\Provider\Service;

use Core42\I18n\Localization\Localization;
use Frontend42\Command\Navigation\CreateFrontendNavigationCommand;
use Frontend42\Navigation\Provider\Provider;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProviderFactory implements FactoryInterface
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
        $defaultLocale = $container->get(Localization::class)->getActiveLocale();

        $cache = $container->get('Cache\Sitemap');

        if (!$cache->hasItem('nav_' . $defaultLocale)) {
            $container->get('Command')->get(CreateFrontendNavigationCommand::class)->run();
        }

        $pages = [];
        if ($cache->hasItem('nav_' . $defaultLocale)) {
            $pages = $cache->getItem('nav_' . $defaultLocale);
        }

        return new Provider($pages, $defaultLocale);
    }
}
