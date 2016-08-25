<?php
namespace Frontend42\Mvc\Controller\Plugins;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Frontend42\Page\Page as PageContainer;


/**
 * @method \Frontend42\Model\Page getPage()
 * @method \Frontend42\Model\Sitemap getSitemap()
 * @method \Frontend42\PageType\PageContent\PageContent getPageContent()
 */
class Page extends AbstractPlugin
{
    /**
     * @var PageContainer
     */
    protected $page;

    /**
     * Page constructor.
     * @param PageContainer $page
     */
    public function __construct(PageContainer $page)
    {
        $this->page = $page;
    }

    /**
     * @return $this
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments = [])
    {
        return call_user_func_array([$this->page, $method], $arguments);
    }

    /**
     * @param string $name
     * @param mixed $default
     */
    public function getParam($name, $default = null)
    {
        $this->page->getPageContent()->getParam($name, $default);
    }
}
