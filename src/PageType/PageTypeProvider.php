<?php
namespace Frontend42\PageType;

use Zend\Form\Factory;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\FormElementManager;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;
use Zend\Stdlib\Glob;

class PageTypeProvider extends AbstractPluginManager
{
    /**
     * @var array
     */
    protected $pageTypes = [];

    /**
     * @var FormElementManager
     */
    protected $formElementManager;

    /**
     * @param $pageTypePaths
     */
    public function loadPageTypes($pageTypePaths)
    {
        foreach ($pageTypePaths as $path) {
            $entries = Glob::glob($path);
            foreach ($entries as $file) {
                $pageTypeOptions = new PageTypeOptions(require_once $file);

                $this->pageTypes[$pageTypeOptions->getHandle()] = $pageTypeOptions;
            }
        }
    }

    /**
     * @param FormElementManager $formElementManager
     */
    public function setFormElementManager(FormElementManager $formElementManager)
    {
        $this->formElementManager = $formElementManager;
    }

    /**
     * @return array
     */
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
     * @return Form
     */
    public function getPageForm($handle)
    {
        $form = new Form();
        $form->setFormFactory(new Factory($this->formElementManager));

        /** @var PageTypeOptions $pageTypeOptions */
        $pageTypeOptions = $this->pageTypes[$handle];

        $forms = $pageTypeOptions->getForm();

        foreach ($forms as $sectionHandle => $subformInfo) {
            $fieldset = new Fieldset($sectionHandle);
            $fieldset->setFormFactory($form->getFormFactory());

            $fieldset->setLabel($subformInfo['label']);
            foreach ($subformInfo['elements'] as $element) {
                $fieldset->add($pageTypeOptions->getElements()[$element]);
            }
            $form->add($fieldset);
        }

        return $form;
    }

    /**
     * @param string $handle
     * @return PageTypeInterface
     */
    public function getPageType($handle)
    {
        /** @var PageTypeOptions $pageTypeOptions */
        $pageTypeOptions = $this->pageTypes[$handle];

        return $this->get($pageTypeOptions->getClass());
    }

    /**
     * @param $handle
     * @return PageTypeOptions
     */
    public function getPageTypeOptions($handle)
    {
        return $this->pageTypes[$handle];
    }

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof PageTypeInterface) {
            return;
        }

        throw new \RuntimeException(sprintf(
            "Plugin of type %s is invalid; must implement \\Frontend42\\PageType\\PageTypeInterface",
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
