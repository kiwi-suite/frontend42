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
     * @var PageVersionSelector
     */
    protected $pageVersionSelector;

    public function __construct(PageTableGateway $pageTableGateway, PageVersionSelector $pageVersionSelector)
    {
        $this->pageTableGateway = $pageTableGateway;
        $this->pageVersionSelector = $pageVersionSelector;
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
    public function loadByPageId($pageId, $version = PageVersionSelector::VERSION_HEAD)
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
    public function getPageById($pageId, $version = PageVersionSelector::VERSION_HEAD)
    {
        return $this->loadByPageId($pageId, $version);
    }
}
