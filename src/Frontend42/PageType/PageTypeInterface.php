<?php
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
