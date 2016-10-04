<?php
namespace Frontend42\PageType\Provider;

use Frontend42\PageType\PageTypeInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Factory\InvokableFactory;

class PageTypeProvider extends AbstractPluginManager
{
    /**
     * @var array
     */
    protected $displayPageTypes = [];

    /**
     * An object type that the created instance must be instanced of
     *
     * @var null|string
     */
    protected $instanceOf = PageTypeInterface::class;

    /**
     * @param string $name
     * @param string $label
     */
    public function addDisplayPageTypes($name, $label)
    {
        $this->displayPageTypes[$name] = $label;
    }

    /**
     * @return array
     */
    public function getDisplayPageTypes()
    {
        return $this->displayPageTypes;
    }

    /**
     * @param string $name
     * @param array|null $options
     * @return mixed
     */
    public function build($name, array $options = null)
    {
        if (!isset($this->factories[$name])) {
            $this->setFactory($name, InvokableFactory::class);
        }

        return parent::build($name, $options);
    }
}
