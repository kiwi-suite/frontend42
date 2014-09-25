<?php
namespace Frontend42\PageType\Service;

use Frontend42\PageType\PageTypeInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;

class PageTypePluginManager extends AbstractPluginManager
{
    /**
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->addAbstractFactory(new PageTypeFallbackAbstractFactory(), false);

        $this->addInitializer(function(PageTypeInterface $instance, $serviceLocator){
            $instance->setSitemapTableGateway($serviceLocator->getServiceLocator()->get('TableGateway')->get('Frontend42\Sitemap'));
            $instance->setPageTableGateway($serviceLocator->getServiceLocator()->get('TableGateway')->get('Frontend42\Page'));
        });
    }

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @throws \RuntimeException
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof PageTypeInterface) {
            return;
        }

        throw new \RuntimeException(sprintf(
            "Plugin of type %s is invalid; must implement \\Core42\\Command\\CommandInterface",
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
