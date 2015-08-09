<?php
namespace Frontend42\View\Helper;

use Frontend42\Model\Page as PageModel;
use Frontend42\Navigation\PageHandler;
use Zend\View\Helper\AbstractHelper;

class PageRoute extends AbstractHelper
{
    /**
     * @var PageHandler
     */
    protected $pageHandler;

    /**
     * @param PageHandler $pageHandler
     */
    public function __construct(PageHandler $pageHandler)
    {
        $this->pageHandler = $pageHandler;
    }

    /**
     * @return $this
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @param int|PageModel $page
     * @return string
     * @throws \Exception
     */
    public function fromPage($page)
    {
        return $this->pageHandler->getRouteByPage($page);
    }

    /**
     * @param string $handle
     * @param string $locale
     * @return string
     */
    public function fromHandle($handle, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getView()->localization()->getActiveLocale();
        }

        return $this->pageHandler->getRouteByHandle($handle, $locale);
    }

    /**
     * @param int|PageModel $page
     * @param string $locale
     * @return string
     * @throws \Exception
     */
    public function switchLanguage($page, $locale)
    {
        return $this->pageHandler->getSwitchLanguageRoute($page, $locale);
    }
}
