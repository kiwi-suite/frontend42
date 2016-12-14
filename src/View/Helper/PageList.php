<?php
namespace Frontend42\View\Helper;

use Frontend42\Selector\PageListSelector;
use Zend\Form\View\Helper\AbstractHelper;

class PageList extends AbstractHelper
{
    /**
     * @var PageListSelector
     */
    private $pageListSelector;

    /**
     * PageList constructor.
     * @param PageListSelector $pageListSelector
     */
    public function __construct(PageListSelector $pageListSelector)
    {
        $this->pageListSelector = $pageListSelector;
    }

    /**
     * @return PageListSelector
     */
    protected function getPageListSelector()
    {
        return clone $this->pageListSelector;
    }

    /**
     * @param int $sitemapId
     * @param string $locale
     * @return array
     */
    public function __invoke($sitemapId, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getView()->localization()->getActiveLocale();
        }

        return $this->getPageListSelector()->setSitemapId((int) $sitemapId)->setLocale($locale)->getResult();
    }
}
