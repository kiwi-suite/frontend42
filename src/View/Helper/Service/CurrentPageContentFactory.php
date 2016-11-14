<?php
namespace Frontend42\View\Helper\Service;

use Core42\Model\GenericModel;
use Core42\View\Helper\Proxy;
use Frontend42\Model\PageContent;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class CurrentPageContentFactory implements FactoryInterface
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
        $routeMatch = $container->get('Application')->getMvcEvent()->getRouteMatch();

        $pageContent = new GenericModel();
        if ($routeMatch->getParam('__pageContent__') instanceof PageContent) {
            $pageContent = $routeMatch->getParam('__pageContent__');
        }


        return new Proxy($pageContent);
    }
}
