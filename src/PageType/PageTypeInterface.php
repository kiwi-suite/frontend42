<?php
namespace Frontend42\PageType;

use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill;

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
    public function getFormDefinition();

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
    public function getElements();

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
     * @return Form
     */
    public function getPageForm();

    /**
     * @param FormElementManagerV3Polyfill $formElementManager
     */
    public function setFormElementManager(FormElementManagerV3Polyfill $formElementManager);

    /**
     * @return PageContent
     */
    public function getPageContent();
}
