<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\View\Helper;

use Core42\Navigation\Container;
use Core42\Navigation\Navigation;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Renderer\RendererInterface;

class Page extends AbstractHelper
{
    /**
     * @var int
     */
    private $pageId;

    /**
     * @var array
     */
    private $params = array();

    /**
     * @var array
     */
    private $cache = array();

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
     * @return \Core42\Navigation\Page\Page|null
     */
    public function getCurrentPage()
    {
        $params = $this->getView()->plugin('params');

        return $this->getPage($params->fromRoute('sitemapId'));
    }

    /**
     * @param $pageId
     * @return \Core42\Navigation\Page\Page|null
     */
    public function getPage($pageId)
    {
        if (array_key_exists($pageId, $this->cache)) {
            return $this->cache[$pageId];
        }
        $page = $this->container->findOneByOption("sitemapId", $pageId);
        $this->cache[$pageId] = $page;

        return $page;
    }

    /**
     * @param null $pageId
     * @param array $params
     * @return $this
     */
    public function __invoke($pageId = null, array $params = array())
    {
        $this->pageId = $pageId;

        $this->params = $params;

        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getRoute()
    {
        $page = $this->getPage($this->pageId);

        if ($page === null) {
            return "";
        }

        return $page->getOption("route");
    }

    /**
     * @return null|string
     */
    public function getHref()
    {
        $page = $this->getPage($this->pageId);

        if ($page === null) {
            return "";
        }

        return $this->navigation->getHref($page);
    }

    /**
     * @return mixed|string
     */
    public function getTitle()
    {
        $page = $this->getPage($this->pageId);

        if ($page === null) {
            return "";
        }

        return $page->getOption("label");
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $page = $this->getPage($this->pageId);
        if ($page === null) {
            return "";
        }

        $this->params = array_merge(
            $this->params,
            array(
                'title' => $page->getOption("label", ""),
                'href' => $this->navigation->getHref($page),
            )
        );

        $attrString = "";
        foreach ($this->params as $key => $value) {
            $attrString .= sprintf('%s="%s"', $key, $this->escape($value));
        }

        return sprintf("<a%s>%s</a>", (!empty($attrString)) ? " ".$attrString : "", $page->getOption("label", ""));
    }

    /**
     * Escape a string
     *
     * @param  string $string
     * @return string
     */
    protected function escape($string)
    {
        if ($this->getView() instanceof RendererInterface
            && method_exists($this->getView(), 'getEncoding')
        ) {
            $escaper = $this->getView()->plugin('escapeHtml');
            return $escaper((string) $string);
        }

        return $string;
    }
}
