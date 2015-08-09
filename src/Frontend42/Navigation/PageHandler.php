<?php
namespace Frontend42\Navigation;

use Frontend42\Model\Page;
use Frontend42\PageType\PageTypeContent;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\PageTableGateway;
use Zend\Json\Json;

class PageHandler
{
    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @var array
     */
    protected $currentInfo;

    /**
     * @var PageTableGateway
     */
    protected $pageTableGateway;

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
     * @var PageVersionSelector
     */
    protected $pageVersionSelector;

    public function __construct(
        PageTableGateway $pageTableGateway,
        PageVersionSelector $pageVersionSelector
    ) {
        $this->pageTableGateway = $pageTableGateway;
        $this->pageVersionSelector = $pageVersionSelector;
    }

    /**
     * @param array $handleMapping
     */
    public function setHandleMapping($handleMapping)
    {
        $this->handleMapping = $handleMapping;
    }

    /**
     * @param string $defaultHandle
     */
    public function setDefaultHandle($defaultHandle)
    {
        $this->defaultHandle = $defaultHandle;
    }

    /**
     * @param array $pageMapping
     */
    public function setPageMapping($pageMapping)
    {
        $this->pageMapping = $pageMapping;
    }

    /**
     * @param int $pageId
     * @param string $version
     */
    public function loadCurrentPage($pageId, $version = PageVersionSelector::VERSION_APPROVED)
    {
        $this->currentInfo = $this->loadByPageId($pageId, $version);
    }

    /**
     * @return array
     */
    public function getCurrentPageInfo()
    {
        return $this->currentInfo;
    }

    /**
     * @param $pageId
     * @param string $version
     * @return array
     * @throws \Exception
     */
    public function loadByPageId($pageId, $version = PageVersionSelector::VERSION_APPROVED)
    {
        $cacheKey = $pageId . '#' . $version;

        if (!array_key_exists($cacheKey, $this->cache)) {
            $page = $this->pageTableGateway->selectByPrimary((int) $pageId);

            $version = $this->pageVersionSelector
                ->setPageId($page->getId())
                ->setVersionName($version)
                ->getResult();

            $pageContent = new PageTypeContent();
            $pageContent->setContent(Json::decode($version->getContent(), Json::TYPE_ARRAY));

            $this->cache[$cacheKey] = [
                'page'      => $page,
                'content'   => $pageContent,
            ];
        }

        return $this->cache[$cacheKey];
    }

    /**
     * @param $pageId
     * @param string $version
     * @return array
     */
    public function getPageById($pageId, $version = PageVersionSelector::VERSION_APPROVED)
    {
        return $this->loadByPageId($pageId, $version);
    }

    /**
     * @param Page|int $page
     * @return mixed
     * @throws \Exception
     */
    public function getRouteByPage($page)
    {
        if ($page instanceof Page) {
            $page = $page->getId();
        }

        $page = (int) $page;

        if (!array_key_exists($page, $this->pageMapping)) {
            return "";
        }

        return $this->pageMapping[$page]['route'];
    }

    /**
     * @param string $handle
     * @param string $locale
     * @return string
     * @throws \Exception
     */
    public function getRouteByHandle($handle, $locale)
    {
        if (empty($this->handleMapping[$handle][$locale])) {
            if ($this->defaultHandle !== $handle) {
                return $this->getRouteByHandle($this->defaultHandle, $locale);
            }
            return "";
        }

        return $this->getRouteByPage($this->handleMapping[$handle][$locale]);
    }

    /**
     * @param $page
     * @param $locale
     * @return mixed
     * @throws \Exception
     */
    public function getSwitchLanguageRoute($page, $locale)
    {
        if ($page instanceof Page) {
            $page = $page->getId();
        }

        $page = (int) $page;

        if (!array_key_exists($page, $this->pageMapping)) {
            if (!empty($this->defaultHandle)) {
                return $this->getRouteByHandle($this->defaultHandle, $locale);
            }

            throw new \Exception("invalid page and no default handle set");
        }

        if (!array_key_exists($locale, $this->pageMapping[$page]['locale'])){
            if (!empty($this->defaultHandle)) {
                return $this->getRouteByHandle($this->defaultHandle, $locale);
            }

            return "";
        }

        return $this->getRouteByPage($this->pageMapping[$page]['locale'][$locale]);
    }
}
