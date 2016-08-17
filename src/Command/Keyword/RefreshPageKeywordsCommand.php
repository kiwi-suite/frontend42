<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Command\Keyword;

use Admin42\Command\Tag\SaveCommand;
use Core42\Command\AbstractCommand;
use Frontend42\Model\PageKeyword;
use Frontend42\TableGateway\PageKeywordTableGateway;

class RefreshPageKeywordsCommand extends AbstractCommand
{

    /**
     * @var int
     */
    protected $pageId;

    /**
     * @var array
     */
    protected $keywords = [];

    /**
     * @param $pageId
     * @return $this
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * @param array $keywords
     * @return $this
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     *
     */
    protected function preExecute()
    {
        $this->keywords = array_map(function ($value) {
            return trim($value);
        }, $this->keywords);
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        if (empty($this->pageId)) {
            return;
        }

        $this->getTableGateway(PageKeywordTableGateway::class)->delete(['pageId' => $this->pageId]);

        if (empty($this->keywords)) {
            return;
        }

        $cmd = $this->getCommand(SaveCommand::class);
        $cmd->setTags(implode(",", $this->keywords))
            ->run();
        foreach ($this->keywords as $keyword) {
            $pageKeyword = new PageKeyword();
            $pageKeyword->setKeyword($keyword)
                ->setPageId($this->pageId);

            $this->getTableGateway(PageKeywordTableGateway::class)->insert($pageKeyword);
        }
    }
}
