<?php
namespace Frontend42\Navigation\Service;

use Core42\I18n\Localization\Localization;
use Core42\Navigation\Container;
use Frontend42\Navigation\Page\Page;
use Frontend42\Page\Data\Data;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Router\RouteStackInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContainerFactory implements FactoryInterface
{
    /**
     * @var RouteStackInterface
     */
    protected $router;

    /**
     * @var Data
     */
    protected $data;

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
        $this->router = $container->get("Router");
        $this->data = $container->get(Data::class);

        $locale = $container->get(Localization::class)->getActiveLocale();
        $pages = $container->get(Data::class)->getNavigation($locale);

        $navigation = new Container();

        foreach ($pages as $pageSpec) {
            $navigation->addPage($this->createPage($pageSpec));
        }
        $navigation->sort();

        return $navigation;
    }

    protected function createPage($pageSpec)
    {
        $page = new Page($this->data, $this->router);
        $page->setLabel((!empty($pageSpec['label'])) ? $pageSpec['label'] : null);
        $page->setOrder((!empty($pageSpec['order'])) ? (int) $pageSpec['order'] : null);
        $page->setSitemapId((!empty($pageSpec['sitemapId'])) ? (int) $pageSpec['sitemapId'] : null);
        $page->setPageId((!empty($pageSpec['pageId'])) ? (int) $pageSpec['pageId'] : null);

        if (!empty($pageSpec['pages'])) {
            foreach ($pageSpec['pages'] as $subPageSpec) {
                $page->addPage($this->createPage($subPageSpec));
            }
        }

        return $page;
    }
}
