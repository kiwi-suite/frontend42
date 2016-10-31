<?php
namespace Frontend42\View\Helper;

use Core42\View\Helper\Proxy;
use Frontend42\Selector\PageSelector;

class Page extends Proxy
{
    /**
     * @var PageSelector
     */
    protected $pageSelector;

    /**
     * Page constructor.
     * @param PageSelector $pageSelector
     */
    public function __construct(PageSelector $pageSelector)
    {
        $this->pageSelector = $pageSelector;
    }

    public function __invoke($pageId = null)
    {
        if ($pageId !== null) {
            $page = $this->pageSelector->setPageId((int)$pageId)->getResult();

            if ($page instanceof \Frontend42\Model\Page) {
                $this->object = $page;
            }
        }

        return $this;
    }
}
