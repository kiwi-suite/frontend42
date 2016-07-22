<?php
namespace Frontend42\Navigation\Provider;

use Core42\Navigation\Container;
use Core42\Navigation\Page\PageFactory;
use Core42\Navigation\Provider\AbstractProvider;
use Frontend42\Selector\SitemapSelector;

class Provider extends AbstractProvider
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $pages;

    /**
     * Provider constructor.
     * @param $pages
     */
    public function __construct($pages)
    {
        $this->pages = $pages;
    }

    /**
     * @param string $containerName
     * @return Container
     */
    public function getContainer($containerName)
    {
        if ($this->container instanceof Container) {
            return $this->container;
        }

        $this->container = new Container();
        $this->container->setContainerName($containerName);

        foreach ($this->pages as $page) {
            $this->container->addPage(PageFactory::create($page, $containerName));
        }

        unset($this->pages);

        $this->container->sort();
        return $this->container;
    }
}
