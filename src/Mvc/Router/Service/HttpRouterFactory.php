<?php
namespace Frontend42\Mvc\Router\Service;

use Admin42\Authentication\AuthenticationService;
use Frontend42\Command\Router\CreateRouteConfigCommand;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Router\Http\TreeRouteStack;
use Zend\Router\RouterConfigTrait;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class HttpRouterFactory implements FactoryInterface
{
    use RouterConfigTrait;
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
        $config = $container->has('config') ? $container->get('config') : [];

        // Defaults
        $class  = TreeRouteStack::class;
        $config = isset($config['router']) ? $config['router'] : [];

        $cache = $container->get('Cache\Sitemap');
        if (!$cache->hasItem('sitemap')) {
            $container->get('Command')->get(CreateRouteConfigCommand::class)->run();
        }

        $authenticationService = $container->get(AuthenticationService::class);
        if ($authenticationService->hasIdentity()) {
            $result = $container->get('Command')->get(CreateRouteConfigCommand::class)
                ->setIncludeOffline(true)
                ->setCaching(false)
                ->run();
            $frontendRoutes = $result['sitemap'];
        } else {
            $frontendRoutes = $cache->getItem("sitemap");
        }

        $frontendRoutes = (empty($frontendRoutes)) ? [] : $frontendRoutes;
        $config['routes']['frontend']['child_routes'] = $frontendRoutes;

        return $this->createRouter($class, $config, $container);
    }
}
