<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Command\Block;

use Core42\Command\AbstractCommand;
use Frontend42\TableGateway\BlockInheritanceTableGateway;

class CleanInheritanceCommand extends AbstractCommand
{
    /**
     * @var int
     */
    protected $sourcePageId;

    /**
     * @var int
     */
    protected $targetPageId;

    /**
     * @var string
     */
    protected $section;

    /**
     * @param int $sourcePageId
     * @return $this
     */
    public function setSourcePageId($sourcePageId)
    {
        $this->sourcePageId = $sourcePageId;

        return $this;
    }

    /**
     * @param string $section
     * @return $this
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $model = $this->getTableGateway(BlockInheritanceTableGateway::class)->select([
            'sourcePageId' => $this->sourcePageId,
            'section' => $this->section,
        ]);
        $model = $model->current();

        $this->getTableGateway(BlockInheritanceTableGateway::class)->delete([
            'sourcePageId' => $this->sourcePageId,
            'section' => $this->section,
        ]);

    }
}
