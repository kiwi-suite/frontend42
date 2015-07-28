<?php
namespace Frontend42\View\Helper;

use Admin42\Authentication\AuthenticationService;
use Frontend42\Model\BlockInheritance;
use Frontend42\PageType\PageTypeContent;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\BlockInheritanceTableGateway;
use Frontend42\TableGateway\PageTableGateway;
use Zend\Json\Json;
use Zend\View\Helper\AbstractHelper;

class Block extends AbstractHelper
{
    /**
     * @var null|array
     */
    protected $relatedPageInfo = null;

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
     * @param BlockInheritanceTableGateway $blockInheritanceTableGateway
     * @param PageVersionSelector $pageVersionSelector
     * @param PageTableGateway $pageTableGateway
     */
    public function __construct(
        BlockInheritanceTableGateway $blockInheritanceTableGateway,
        PageVersionSelector $pageVersionSelector,
        PageTableGateway $pageTableGateway
    ) {
        $this->blockInheritanceTableGateway = $blockInheritanceTableGateway;

        $this->pageVersionSelector = $pageVersionSelector;

        $this->pageTableGateway = $pageTableGateway;
    }

    /**
     * @param array|bool|false $blockData
     * @param null $section
     * @return $this|string
     */
    public function __invoke($blockData = false, $section =  null)
    {
        if ($blockData === false) {

            return $this;
        }

        if ($section !== null) {
            $page = $this->view->plugin('page');
            $blockData = $this->getBlockData(
                $blockData,
                $page->getPage()->getId(),
                $section
            );
        }

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
     * @param $pageId
     * @param $section
     * @return array|null
     * @throws \Exception
     */
    public function getRelatedPageInfo($pageId, $section)
    {
        $this->loadRelatedPageInfo();

        if (empty($this->relatedPageInfo[$pageId][$section])) {
            return false;
        }

        $page = $this->pageTableGateway->selectByPrimary(
            $this->relatedPageInfo[$pageId][$section]
        );

        return [
            'id' => $page->getId(),
            'name' => $page->getName(),
        ];
    }

    public function loadRelatedPageInfo()
    {
        if ($this->relatedPageInfo !== null) {
            return;
        }

        $this->relatedPageInfo = [];

        $result = $this->blockInheritanceTableGateway->select();

        /** @var BlockInheritance $_blockInheritance */
        foreach ($result as $_blockInheritance) {
            $this->relatedPageInfo[$_blockInheritance->getSourcePageId()][$_blockInheritance->getSection()] = $_blockInheritance->getTargetPageId();
        }
    }

    public function getBlockData($blockData, $pageId, $section)
    {
        $this->loadRelatedPageInfo();
        if (empty($this->relatedPageInfo[$pageId][$section])) {
            return $blockData;
        }

        $version = $this->pageVersionSelector
            ->setVersionName(PageVersionSelector::VERSION_HEAD)
            ->setPageId($this->relatedPageInfo[$pageId][$section])
            ->getResult();

        $pageContent = new PageTypeContent();
        $pageContent->setContent(Json::decode($version->getContent(), Json::TYPE_ARRAY));

        return $pageContent->getParam($section, $blockData);
    }

}
