<?php
namespace Frontend42\PageType\Service;

use Frontend42\PageType\PageTypeProvider;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PageTypeProviderFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config')['page_types'];

        $pageTypeProvider = new PageTypeProvider(new Config($config['service_manager']));
        $pageTypeProvider->loadPageTypes($config['paths']);
        $pageTypeProvider->setFormElementManager($serviceLocator->get("FormElementManager"));

        $pageTypeProvider->addInitializer(function ($instance) use ($serviceLocator) {
            if (method_exists($instance, 'setKeywordCommand')) {
                $instance
                    ->setKeywordCommand(
                        $serviceLocator
                            ->get('Command')
                            ->get('Frontend42\Keyword\RefreshPageKeywords')
                    );
            }

            if (method_exists($instance, 'setPageHandler')) {
                $instance
                    ->setPageHandler(
                        $serviceLocator
                            ->get('Frontend42\Navigation\PageHandler')
                    );
            }
        });

        return $pageTypeProvider;
    }
}
