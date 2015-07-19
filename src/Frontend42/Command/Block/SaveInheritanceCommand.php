<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Command\Block;


use Frontend42\Model\BlockInheritance;

class SaveInheritanceCommand extends \Core42\Command\AbstractCommand
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
     * @param int $targetPageId
     * @return $this
     */
    public function setTargetPageId($targetPageId)
    {
        $this->targetPageId = $targetPageId;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $result = $this->getTableGateway('Frontend42\BlockInheritance')->select([
            'sourcePageId' => $this->targetPageId,
            'section' => $this->section,
        ]);

        if ($result->count() > 0) {
            $this->targetPageId = $result->current()->getTargetPageId();
        }

        $blockInheritance= new BlockInheritance();
        $blockInheritance->setSourcePageId($this->sourcePageId)
            ->setTargetPageId($this->targetPageId)
            ->setSection($this->section);

        $this->getTableGateway('Frontend42\BlockInheritance')->insert($blockInheritance);
    }
}
