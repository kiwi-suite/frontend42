<?php
namespace Frontend42\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Block extends AbstractHelper
{
    /**
     * @param null|array $blockData
     * @return string
     */
    public function __invoke($blockData)
    {
        $html = [];
        $partialHelper = $this->view->plugin('partial');

        $blockData = (empty($blockData)) ? [] : $blockData;

        foreach ($blockData as $_block) {
            $html[] = $partialHelper('block/'. $_block['dynamic_type'], $_block);
        }

        return implode(PHP_EOL, $html);
    }

}
