<?php
namespace Frontend42\View\Helper;

use Frontend42\Navigation\PageHandler;
use Zend\View\Helper\AbstractHelper;

class Page extends AbstractHelper
{
    /**
     * @var PageHandler
     */
    protected $pageHandler;

    /**
     * @var
     */
    protected $selectedPage;

    /**
     * @param PageHandler $pageHandler
     */
    public function __construct(PageHandler $pageHandler)
    {
        $this->pageHandler = $pageHandler;
    }

    /**
     * @param null|int|string $pageId
     * @return $this
     */
    public function __invoke($pageId = null)
    {
        if ($pageId === null) {
            $this->selectedPage = $this->pageHandler->getCurrentPageInfo();
        } elseif (is_int($pageId)) {
            $this->selectedPage = $this->pageHandler->getPageById($pageId);
        } else {

        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        return $this->selectedPage['content']->getParam($name, $default);
    }
}
