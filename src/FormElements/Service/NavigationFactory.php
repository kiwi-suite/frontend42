<?php
namespace Frontend42\FormElements\Service;

use Admin42\FormElements\MultiCheckbox;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class NavigationFactory implements FactoryInterface
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
        $nav = [];
        $config = $container->get("config")["navigation"]["nav"];
        foreach ($config as $name => $item) {
            $label = (isset($item['label'])) ? $item['label'] : $name;
            $nav[$name] = $label;
        }

        /** @var MultiCheckbox $navigation */
        $navigation = $container->get('FormElementManager')->get(MultiCheckbox::class);
        $navigation->setValueOptions($nav);

        return $navigation;
    }
}
