<?php
namespace Frontend42\View\Helper;

use Frontend42\Model\Page as PageModel;
use Zend\View\Helper\AbstractHelper;

class PageRoute extends AbstractHelper
{
    /**
     * @var array
     */
    protected $pageMapping;

    /**
     * @var array
     */
    protected $handleMapping;

    /**
     * @var string
     */
    protected $defaultHandle;

    /**
     * @param array $pageMapping
     * @param array $handleMapping
     * @param string $defaultHandle
     */
    public function __construct(array $pageMapping, array $handleMapping, $defaultHandle)
    {
        $this->pageMapping = $pageMapping;

        $this->handleMapping = $handleMapping;

        $this->defaultHandle = $defaultHandle;
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
        if ($page instanceof PageModel) {
            $page = $page->getId();
        }

        $page = (int) $page;

        if (!array_key_exists($page, $this->pageMapping)) {
            throw new \Exception("invalid page");
        }

        return $this->pageMapping[$page]['route'];
    }

    /**
     * @param string $handle
     * @param string $locale
     * @return string
     * @throws \Exception
     */
    public function fromHandle($handle, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getView()->localization()->getActiveLocale();
        }

        if (empty($this->handleMapping[$handle][$locale])) {
            if ($this->defaultHandle !== $handle) {
                return $this->fromHandle($this->defaultHandle, $locale);
            }
            throw new \Exception("invalid handle/locale");
        }

        return $this->fromPage($this->handleMapping[$handle][$locale]);
    }

    /**
     * @param int|PageModel $page
     * @param string $locale
     * @return string
     * @throws \Exception
     */
    public function switchLanguage($page, $locale)
    {
        if ($page instanceof PageModel) {
            $page = $page->getId();
        }

        $page = (int) $page;

        if (!array_key_exists($page, $this->pageMapping)) {
            if (!empty($this->defaultHandle)) {
                return $this->fromHandle($this->defaultHandle, $locale);
            }

            throw new \Exception("invalid page and no default handle set");
        }

        if (!array_key_exists($locale, $this->pageMapping[$page]['locale'])){
            if (!empty($this->defaultHandle)) {
                return $this->fromHandle($this->defaultHandle, $locale);
            }

            throw new \Exception("invalid locale and no default handle set");
        }

        return $this->fromPage($this->pageMapping[$page]['locale'][$locale]);
    }
}
