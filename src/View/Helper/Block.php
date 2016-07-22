<?php
namespace Frontend42\View\Helper;

use Frontend42\PageType\PageTypeContent;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\BlockInheritanceTableGateway;
use Frontend42\TableGateway\PageTableGateway;
use Zend\Cache\Storage\StorageInterface;
use Zend\Json\Json;
use Zend\View\Helper\AbstractHelper;

class Block extends AbstractHelper
{
    /**
     * @var BlockInheritanceTableGateway
     */
    protected $blockInheritanceTableGateway;

    /**
     * @var PageVersionSelector
     */
    protected $pageVersionSelector;

    /**
     * @var PageTableGateway
     */
    protected $pageTableGateway;

    /**
     * @var StorageInterface
     */
    protected $cache;

    /**
     * @param BlockInheritanceTableGateway $blockInheritanceTableGateway
     * @param PageVersionSelector $pageVersionSelector
     * @param PageTableGateway $pageTableGateway
     * @param StorageInterface $cache
     */
    public function __construct(
        BlockInheritanceTableGateway $blockInheritanceTableGateway,
        PageVersionSelector $pageVersionSelector,
        PageTableGateway $pageTableGateway,
        StorageInterface $cache
    ) {
        $this->blockInheritanceTableGateway = $blockInheritanceTableGateway;

        $this->pageVersionSelector = $pageVersionSelector;

        $this->pageTableGateway = $pageTableGateway;

        $this->cache = $cache;
    }

    /**
     * @param array|bool|false $blockData
     * @param null $section
     * @return $this|string
     */
    public function __invoke($blockData = false, $section = null)
    {
        if ($blockData === false) {

            return $this;
        }

        $blockData = $this->getCurrentBlockData($blockData, $section);

        $html = [];
        $partialHelper = $this->view->plugin('partial');

        $blockData = (empty($blockData)) ? [] : $blockData;

        foreach ($blockData as $_block) {
            $partialFilename = 'block/'. $_block['dynamic_type'];

            $resolved = $this->getView()->resolver($partialFilename);
            if ($resolved === false) {
                continue;
            }

            $html[] = $partialHelper($partialFilename, $_block);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param $blockData
     * @param null $section
     * @return array
     */
    public function getCurrentBlockData($blockData, $section = null)
    {
        if ($section !== null) {
            $page = $this->view->plugin('page');
            $blockData = $this->getBlockData(
                $blockData,
                $page->getPage()->getId(),
                $section
            );
        }

        return $this->cleanUpBlockData($blockData);
    }

    /**
     * @param $blockData
     * @return array
     */
    protected function cleanUpBlockData($blockData)
    {
        if (!is_array($blockData)) {
            return [];
        }

        foreach ($blockData as $_key => $_block) {
            if (is_array($_block)) {
                if (!array_key_exists('dynamic_deleted', $_block) || $_block['dynamic_deleted'] == 'true') {
                    unset($blockData[$_key]);
                } else {
                    foreach ($_block as $_subKey => $_subBlock) {
                        if (is_array($_subBlock)) {
                            $blockData[$_key][$_subKey] = $this->cleanUpBlockData($_subBlock);
                        }
                    }
                }
            }
        }

        return $blockData;
    }

    /**
     * @param $pageId
     * @param $section
     * @return array|null
     * @throws \Exception
     */
    public function getRelatedPageInfo($pageId, $section)
    {
        $targetPageId = $this->getRelatedPageId($pageId, $section);
        if ($targetPageId === false) {
            return false;
        }

        $page = $this->pageTableGateway->selectByPrimary(
            $targetPageId
        );

        return [
            'id' => $page->getId(),
            'name' => $page->getName(),
        ];
    }

    /**
     * @param $pageId
     * @param $section
     * @return mixed
     */
    public function loadRelatedPageInfo($pageId, $section)
    {
        $cacheKey = "block_inheritance_" . $pageId . '_' . $section;
        if (!$this->cache->hasItem($cacheKey)) {
            $inheritance = false;

            $targetPageId = $this->getRelatedPageId($pageId, $section);

            if ($targetPageId !== false) {
                $version = $this->pageVersionSelector
                    ->setVersionName(PageVersionSelector::VERSION_APPROVED)
                    ->setPageId($targetPageId)
                    ->getResult();

                $pageContent = new PageTypeContent();
                $pageContent->setContent(Json::decode($version->getContent(), Json::TYPE_ARRAY));

                $inheritance = $pageContent->getParam($section, []);
            }

            $this->cache->setItem($cacheKey, $inheritance);
        }

        return $this->cache->getItem($cacheKey);
    }

    /**
     * @param $pageId
     * @param $section
     * @return bool
     */
    public function getRelatedPageId($pageId, $section)
    {
        $result = $this->blockInheritanceTableGateway->select([
            'sourcePageId' => (int) $pageId,
            'section'   => $section,
        ]);

        if ($result->count() == 0) {
            return false;
        }

        $blockInheritance = $result->current();

        return $blockInheritance->getTargetPageId();
    }

    /**
     * @param $blockData
     * @param $pageId
     * @param $section
     * @return array
     */
    public function getBlockData($blockData, $pageId, $section)
    {
        $result = $this->loadRelatedPageInfo($pageId, $section);
        if ($result === false) {
            return $this->cleanUpBlockData($blockData);
        }

        return $this->cleanUpBlockData($result);
    }
}
