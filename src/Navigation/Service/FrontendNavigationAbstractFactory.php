<?php
namespace Frontend42\Navigation\Service;

use Core42\I18n\Localization\Localization;
use Core42\Navigation\Container;
use Frontend42\Navigation\Page;
use Frontend42\Router\PageRoute;
use Frontend42\Selector\NavigationSelector;
use Frontend42\Selector\PageSelector;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Router\Http\RouteMatch;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class FrontendNavigationAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var PageSelector
     */
    protected $pageSelector;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var PageRoute
     */
    protected $pageRoute;

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return array_key_exists($requestedName, $container->get("config")["navigation"]["nav"]);
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
        $this->pageSelector = $container->get('Selector')->get(PageSelector::class);
        $this->routeMatch = $container->get('Application')->getMvcEvent()->getRouteMatch();
        $this->pageRoute = $container->get(PageRoute::class);

        /** @var Localization $localization */
        $localization = $container->get(Localization::class);

        $navigation = $container
            ->get('Selector')
            ->get(NavigationSelector::class)
            ->setLocale($localization->getActiveLocale())
            ->setNavigation($requestedName)
            ->getResult();

        $menu = new Container();

        foreach ($navigation as $nav) {
            $menu->addPage($this->createPage($nav));
        }

        return $menu;
    }

    /**
     * @param array $spec
     * @return Page
     */
    protected function createPage(array $spec)
    {
        $page = new Page(
            $this->pageSelector,
            $this->pageRoute,
            $this->routeMatch
        );
        $page->setPageId($spec['pageId']);

        if (!empty($spec['children'])) {
            foreach ($spec['children'] as $subPageSpec) {
                $page->addPage($this->createPage($subPageSpec));
            }
        }

        return $page;
    }
}
