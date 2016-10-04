<?php
namespace Frontend42\PageType\Service;

use Admin42\FormElements\Fieldset;
use Admin42\FormElements\Form;

class PageForm
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * PageForm constructor.
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    public function create($spec)
    {
        $form = clone $this->form;
        $factory = $this->form->getFormFactory();
        foreach ($spec as $fieldsetName => $options) {
            $fieldset = $factory->create([
                'name' => 'fieldset'.$fieldsetName,
                'type' => Fieldset::class,
                'label' => $options['label'],
                'elements' => $options['elements'],
            ]);

            $form->add($fieldset);
        }
        return $form;
    }
}
