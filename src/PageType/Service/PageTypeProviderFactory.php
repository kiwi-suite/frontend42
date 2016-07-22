<?php
namespace Frontend42\PageType\Service;

use Frontend42\Command\Keyword\RefreshPageKeywordsCommand;
use Frontend42\PageType\PageTypeProvider;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class PageTypeProviderFactory implements FactoryInterface
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
        $config = $container->get('config')['page_types'];

        $pageTypeProvider = new PageTypeProvider($container);
        $pageTypeProvider->loadPageTypes($config['paths']);
        $pageTypeProvider->setFormElementManager($container->get("FormElementManager"));

        $pageTypeProvider->addInitializer(function ($instance) use ($container) {
            if (method_exists($instance, 'setKeywordCommand')) {
                $instance
                    ->setKeywordCommand(
                        $container
                            ->get('Command')
                            ->get(RefreshPageKeywordsCommand::class)
                    );
            }

            if (method_exists($instance, 'setPageHandler')) {
                $instance
                    ->setPageHandler(
                        $container
                            ->get('Frontend42\Navigation\PageHandler')
                    );
            }
        });

        return $pageTypeProvider;
    }
}
