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
        asort($pageTypes);

        $current = array_keys($pageTypes);
        $current = current($current);

        if (count($pageTypes) > 1) {
            $this->add([
                'name'      => 'pageType',
                'label'     => 'frontend42.label.pageType',
                'type'      => 'select',
                'required'  => true,
                'values'    => $pageTypes,
                'value'     => $current,
            ]);
        } else {
            $this->add([
                'name'              => 'pageType',
                'label'             => 'frontend42.label.pageType',
                'type'              => 'hidden',
                'required'          => true,
                'value'             => $current,
                'staticControl'     => true,
                'staticControlText' => $pageTypes[$current],
            ]);
        }

        $this->add([
            'name'      => 'name',
            'label'     => 'frontend42.label.name',
            'type'      => 'text',
            'required'  => true,
        ]);
    }
}
