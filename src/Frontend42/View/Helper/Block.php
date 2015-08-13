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

    public function getCurrentBlockData($blockData, $section =  null)
    {
        if ($section !== null) {
            $page = $this->view->plugin('page');
            $blockData = $this->getBlockData(
                $blockData,
                $page->getPage()->getId(),
                $section
            );
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

    public function getBlockData($blockData, $pageId, $section)
    {
        $targetPageId = $this->getRelatedPageId($pageId, $section);
        if ($targetPageId === false) {
            return $blockData;
        }

        $version = $this->pageVersionSelector
            ->setVersionName(PageVersionSelector::VERSION_HEAD)
            ->setPageId($targetPageId)
            ->getResult();

        $pageContent = new PageTypeContent();
        $pageContent->setContent(Json::decode($version->getContent(), Json::TYPE_ARRAY));

        return $pageContent->getParam($section, $blockData);
    }

}
