<?php
namespace Frontend42\PageType;

use Zend\Form\Form;
use Zend\Stdlib\Glob;

class PageTypeProvider
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $pageTypes = [];

    public function __construct(array $config)
    {
        $this->config = $config;

        $this->loadPageTypes();
    }

    protected function loadPageTypes()
    {
        foreach ($this->config['paths'] as $path) {
            $entries = Glob::glob($path);
            foreach ($entries as $file) {
                $pageTypeOptions = new PageTypeOptions(require_once $file);

                $this->pageTypes[$pageTypeOptions->getHandle()] = $pageTypeOptions;
            }
        }
    }

    public function getDisplayPageTypes()
    {
        $pageTypeDisplay = [];

        /** @var PageTypeOptions $pageTypeOptions */
        foreach ($this->pageTypes as $pageTypeOptions) {
            $pageTypeDisplay[$pageTypeOptions->getHandle()] = $pageTypeOptions->getName();
        }

        return $pageTypeDisplay;
    }

    /**
     * @param string $handle
     * @return array
     */
    public function getDisplayFormSections($handle)
    {
        /** @var PageTypeOptions $pageTypeOptions */
        $pageTypeOptions = $this->pageTypes[$handle];

        $formSections = [];

        foreach ($pageTypeOptions->getForm() as $formSectionHandle => $formArray) {
            $formSections[$formSectionHandle] = $formArray['label'];
        }

        return $formSections;
    }

    /**
     * @param string $handle
     * @param string $section
     * @return Form
     */
    public function getPageForm($handle, $section = null)
    {
        $form = new Form();

        /** @var PageTypeOptions $pageTypeOptions */
        $pageTypeOptions = $this->pageTypes[$handle];

        $forms = $pageTypeOptions->getForm();

        $currentSection = current($forms);
        if ($section !== null) {
            $currentSection = $forms[$section];
        }



        foreach ($currentSection['elements'] as $element) {
            $form->add($pageTypeOptions->getElements()[$element]);
        }

        return $form;
    }

    public function getPageType($pageTypeHandle)
    {
        return new Page();
    }
}
