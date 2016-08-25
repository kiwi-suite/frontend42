<?php
namespace Frontend42\PageType\Provider\Service;

use Frontend42\PageType\Provider\PageTypeProvider;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\Glob;

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

        $pageTypeOptions = [];
        foreach ($container->get('config')['page_types']['paths'] as $path) {
            $entries = Glob::glob($path);
            foreach ($entries as $file) {
                $options = require $file;

                $pageTypeName = pathinfo($file, PATHINFO_FILENAME);
                $options['name'] = $pageTypeName;

                foreach (['label', 'class'] as $check) {
                    if (empty($options[$check])) {
                        throw new \Exception(sprintf(
                            "No config parameter '%s' found in '%s'",
                            $check,
                            $file
                        ));
                    }
                }

                $pageTypeProvider->addDisplayPageTypes($pageTypeName, $options['label']);
                $pageTypeProvider->setAlias($pageTypeName, $options['class']);

                $pageTypeOptions[$options['class']] = $options;
                unset($pageTypeOptions[$options['class']]['class']);
            }
        }

        $pageTypeProvider->addInitializer(function ($container, $pageType) use ($pageTypeOptions){
            $pageType->setFormElementManager($container->get('FormElementManager'));

            if (!($pageType instanceof AbstractOptions)) {
                return;
            }

            if (!isset($pageTypeOptions[get_class($pageType)])) {
                throw new \Exception(sprintf(
                    "No config found for requested pageType '%s'",
                    get_class($pageType)
                ));
            }

            $pageType->setFromArray($pageTypeOptions[get_class($pageType)]);
        });

        return $pageTypeProvider;
    }
}
