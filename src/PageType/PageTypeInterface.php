<?php
namespace Frontend42\PageType;

use Core42\Form\Service\FormElementManager;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;

interface PageTypeInterface
{
    /**
     * @return string
     */
    public function getHandle();

    /**
     * @return string
     */
    public function getController();

    /**
     * @return string
     */
    public function getAction();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getLabel();

    /**
     * @return array
     */
    public function getSections();

    /**
     * @return boolean
     */
    public function getTerminal();

    /**
     * @return boolean
     */
    public function getExclude();

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param Page $page
     * @param PageContent $pageContent
     * @param Sitemap $sitemap
     * @return array
     */
    public function getRouting(Page $page, PageContent $pageContent, Sitemap $sitemap);

    /**
     * @return PageContent
     */
    public function getPageContent();
}
