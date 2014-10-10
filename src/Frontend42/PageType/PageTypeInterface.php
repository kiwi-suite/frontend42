<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\PageType;

use Frontend42\Form\PageAddForm;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;

interface PageTypeInterface
{
    public function setSitemapTableGateway(SitemapTableGateway $sitemapTableGateway);

    public function setPageTableGateway(PageTableGateway $pageTableGateway);

    public function saveInitForm(PageAddForm $form, $locale);

    public function getEditForm($id, $locale);

    public function saveEditForm($data, $id, $locale, $approved);
}
