<?php
namespace Frontend42\PageType\Service;

use Frontend42\PageType\PageTypeInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Factory\InvokableFactory;

class PageTypePluginManager extends AbstractPluginManager
{
    /**
     * @var array
     */
    protected $pageTypes = [];

    /**
     * @var string
     */
    protected $instanceOf = PageTypeInterface::class;

    /**
     * PageTypePluginManager constructor.
     * @param null $configInstanceOrParentLocator
     * @param array $pageTypes
     * @param array $config
     */
    public function __construct($configInstanceOrParentLocator = null, array $pageTypes, array $config = [])
    {
        parent::__construct($configInstanceOrParentLocator, $config);
        $this->addAbstractFactory(new PageTypeAbstractFactory());

        $this->pageTypes = $pageTypes;
    }

    /**
     * @param string $name
     * @param array|null $options
     * @return mixed
     */
    public function build($name, array $options = null)
    {
        if (!$this->has($name) && $this->autoAddInvokableClass === true && class_exists($name)) {
            $this->setFactory($name, InvokableFactory::class);
        }
        return parent::build($name, $options);
    }

    /**
     * @return array
     */
    public function getAvailablePageTypes()
    {
        return $this->pageTypes;
    }
}
