<?php
namespace Frontend42\Form;

use Admin42\FormElements\Wysiwyg;
use File42\FormElements\FileSelector;
use Zend\Form\Element\Text;
use Zend\Form\Form;

class ContentForm extends Form
{
    public function init()
    {
        $this->setName("elementFields");
        $this->setWrapElements(true);

        $form = new Form(uniqid());
        $form->setWrapElements(true);

        $title = new Text("subtitle");
        $title->setLabel("label.subtitle");
        $form->add($title);

        $fileSelector = new FileSelector("image");
        $fileSelector->setLabel("label.image");
        $form->add($fileSelector);

        $wysiwyg = new Wysiwyg("text");
        $wysiwyg->setLabel("label.text");
        $form->add($wysiwyg);

        $this->add($form);
    }
}
