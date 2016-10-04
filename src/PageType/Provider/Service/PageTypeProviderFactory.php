<?php
namespace Frontend42\PageType\Provider\Service;

use Frontend42\PageType\Provider\PageTypeConfigProvider;
use Frontend42\PageType\Provider\PageTypeProvider;
use Interop\Container\ContainerInterface;
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
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $pageTypeProvider = new PageTypeProvider(
            $container,
            $container->get('config')['page_types']['service_manager']
        );

        $pageTypeProvider->addAbstractFactory(new PageTypeFallbackAbstractFactory());

        $pageTypeOptions = $container->get(PageTypeConfigProvider::class)->getPageTypeOptions();
        foreach ($pageTypeOptions as $pageTypeOption) {
            $pageTypeProvider->addDisplayPageTypes($pageTypeOption['name'], $pageTypeOption['label']);
        }

        return $pageTypeProvider;
    }
}
