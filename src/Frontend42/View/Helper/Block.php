<?php
namespace Frontend42\View\Helper;

use Frontend42\Model\BlockInheritance;
use Frontend42\TableGateway\BlockInheritanceTableGateway;
use Frontend42\TableGateway\PageTableGateway;
use Zend\View\Helper\AbstractHelper;

class Block extends AbstractHelper
{
    /**
     * @var BlockInheritanceTableGateway
     */
    protected $blockInheritanceTableGateway;

    /**
     * @var PageTableGateway
     */
    protected $pageTableGateway;

    /**
     * @param BlockInheritanceTableGateway $blockInheritanceTableGateway
     * @param PageTableGateway $pageTableGateway
     */
    public function __construct(
        BlockInheritanceTableGateway $blockInheritanceTableGateway,
        PageTableGateway $pageTableGateway
    ) {
        $this->blockInheritanceTableGateway = $blockInheritanceTableGateway;

        $this->pageTableGateway = $pageTableGateway;

    }

    /**
     * @param false|array $blockData
     * @return string
     */
    public function __invoke($blockData = false)
    {
        if ($blockData === false) {

            return $this;
        }

        $html = [];
        $partialHelper = $this->view->plugin('partial');

        $blockData = (empty($blockData)) ? [] : $blockData;

        foreach ($blockData as $_block) {
            $html[] = $partialHelper('block/'. $_block['dynamic_type'], $_block);
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
        $result = $this->blockInheritanceTableGateway->select(['sourcePageId' => $pageId, 'section' => $section]);

        if ($result->count() == 0) {
            return false;
        }

        $blockInheritance = $result->current();

        $page = $this->pageTableGateway->selectByPrimary($blockInheritance->getTargetPageId());

        return [
            'id' => $page->getId(),
            'name' => $page->getName(),
        ];
    }

}
