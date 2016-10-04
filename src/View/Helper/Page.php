<?php
namespace Frontend42\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Frontend42\Page\Page as PageContainer;

class Page extends AbstractHelper
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
     * @return mixed|null
     */
    public function getParam($name, $default = null)
    {
        $pageContent =  $this->page->getPageContent();
        if ($pageContent === null) {
            return $default;
        }
        return $this->page->getPageContent()->getParam($name, $default);
    }
}
