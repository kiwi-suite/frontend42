<?php
namespace Frontend42\FormElements\Service;

use Admin42\FormElements\Switcher;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class OnlineSwitcherFactory implements FactoryInterface
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
        /** @var Switcher $switcher */
        $switcher = $container->get('FormElementManager')->get(Switcher::class);
        $switcher->setCheckedValue('online');
        $switcher->setUncheckedValue('offline');

        return $switcher;
    }
}
