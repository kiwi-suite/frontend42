<?php
namespace Frontend42\PageType\Provider;

use Frontend42\PageType\PageTypeInterface;
use Zend\ServiceManager\AbstractPluginManager;

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
     * Should the services be shared by default?
     *
     * @var bool
     */
    protected $sharedByDefault = false;

    /**
     * @var array
     */
    protected $aliasInstances = [];

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
    public function get($name, array $options = null)
    {
        if (isset($this->aliases[$name])) {
            if (!isset($this->aliasInstances[$name])) {
                $this->aliasInstances[$name] = parent::get($name, $options);
            }
            return $this->aliasInstances[$name];
        }
        return parent::get($name, $options);
    }
}
