<?php
namespace Frontend42\View\Helper\Service;

use Core42\View\Helper\Proxy;
use Frontend42\Model\Page;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class CurrentPageFactory implements FactoryInterface
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
        $page = new Page();
        $routeMatch = $container->get('Application')->getMvcEvent()->getRouteMatch();
        if ($routeMatch->getParam('__page__') instanceof Page) {
            $page = $routeMatch->getParam('__page__');
        }

        pri

        return new Proxy($page);
    }
}
