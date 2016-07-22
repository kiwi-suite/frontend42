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
        } elseif (intval($pageId) > 0) {
            $this->selectedPage = $this->pageHandler->getPageById((int)$pageId);
        } else {

        }

        return $this;
    }

    /**
     * @return \Frontend42\Model\Page
     */
    public function getCurrentPage()
    {
        return $this->pageHandler->getCurrentPageInfo()['page'];
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        if (empty($this->selectedPage['content'])) {
            return $default;
        }
        return $this->selectedPage['content']->getParam($name, $default);
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        if (empty($this->selectedPage['page'])) {
            return new \Frontend42\Model\Page();
        }
        return $this->selectedPage['page'];
    }
}
