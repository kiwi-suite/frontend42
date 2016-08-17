<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Command\Block;

use Frontend42\Event\BlockEvent;
use Frontend42\TableGateway\BlockInheritanceTableGateway;

class CleanInheritanceCommand extends \Core42\Command\AbstractCommand
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

        $this
            ->getServiceManager()
            ->get('Frontend42\Block\EventManager')
            ->trigger(BlockEvent::EVENT_DELETE_INHERITANCE, $model);

        $this
            ->getServiceManager()
            ->get('Cache\Block')
            ->removeItem('block_inheritance_' . $this->sourcePageId . '_' . $this->section);
    }
}
