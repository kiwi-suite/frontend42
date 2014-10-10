<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Mvc\Controller\Plugin;

use Core42\Navigation\Container;
use Core42\Navigation\Navigation;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Page extends AbstractPlugin
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Navigation
     */
    private $navigation;

    /**
     * @param Container $container
     * @param Navigation $navigation
     */
    public function __construct(Container $container, Navigation $navigation)
    {
        $this->navigation = $navigation;

        $this->container = $container;
    }

    /**
     * @param int $pageId
     * @return $this|\Core42\Navigation\Page\Page|null
     */
    public function __invoke($pageId = null)
    {
        if ($pageId !== null) {
            return $this->getPage($pageId);
        }

        return $this;
    }

    /**
     * @param int $pageId
     * @return \Core42\Navigation\Page\Page|null
     */
    public function getPage($pageId)
    {
        return $this->container->findOneByOption("sitemapId", $pageId);
    }

    /**
     * @param int $pageId
     * @return mixed|string
     */
    public function getRoute($pageId)
    {
        $page = $this->getPage($pageId);

        if ($page == null) {
            return "";
        }

        return $page->getOption("route");
    }
}
