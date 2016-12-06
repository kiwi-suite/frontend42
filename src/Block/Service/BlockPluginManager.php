<?php
namespace Frontend42\Block\Service;

use Frontend42\Block\BlockInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Factory\InvokableFactory;

class BlockPluginManager extends AbstractPluginManager
{
    /**
     * @var array
     */
    protected $blocks = [];

    /**
     * @var string
     */
    protected $instanceOf = BlockInterface::class;

    /**
     * PageTypePluginManager constructor.
     * @param null $configInstanceOrParentLocator
     * @param array $blocks
     * @param array $config
     */
    public function __construct($configInstanceOrParentLocator = null, array $blocks, array $config = [])
    {
        parent::__construct($configInstanceOrParentLocator, $config);
        $this->addAbstractFactory(new BlockAbstractFactory());

        $this->blocks = $blocks;
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
    public function getAvailableBlocks()
    {
        return $this->blocks;
    }
}
