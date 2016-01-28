<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\FormElements;

use Zend\Form\Element;

class PageSelector extends Element
{
    /**
     * @var array
     */
    protected $sitemapData = [];

    /**
     * @param array $sitemapData
     */
    public function setSitemapData(array $sitemapData)
    {
        $this->sitemapData = $sitemapData;
    }

    /**
     * @return array
     */
    public function getSitemapData()
    {
        return $this->sitemapData;
    }
}
