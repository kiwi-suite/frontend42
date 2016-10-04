<?php
namespace Frontend42\Mvc\Router\Service;

use Admin42\Module;
use Core42\Mvc\Environment\Environment;
use Frontend42\Page\Data\Data;
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

        if (!$container->get(Environment::class)->is(Module::ENVIRONMENT_ADMIN)) {
            $frontendRoutes = $container->get(Data::class)->getRouting();

            $frontendRoutes = (empty($frontendRoutes)) ? [] : $frontendRoutes;
            $config['routes']['frontend']['child_routes'] = $frontendRoutes;
        }

        //print_r($config['routes']['frontend']);
        //die();

        return $this->createRouter($class, $config, $container);
    }
}
