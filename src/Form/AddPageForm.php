<?php
namespace Frontend42\Form;

use Admin42\FormElements\Form;

class AddPageForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'csrf',
            'type' => 'csrf'
        ]);
    }

    public function addDefaultElements(array $pageTypes)
    {
        $this->add([
            'name'      => 'pageType',
            'label'     => 'frontend42.label.pageType',
            'type'      => 'select',
            'required'  => true,
            'values'    => $pageTypes,
        ]);

        $this->add([
            'name'      => 'name',
            'label'     => 'frontend42.label.name',
            'type'      => 'text',
            'required'  => true,
        ]);
    }
}
