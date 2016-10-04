<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Form\Sitemap;

use Admin42\FormElements\Form;

class CreateForm extends Form
{
    /**
     *
     */
    public function init()
    {
        $this->add([
            'name' => 'name',
            'type' => 'text',
            'label' => 'label.name',
        ]);

        $this->add([
            'name' => 'pageTypeSelector',
            'type' => 'pageTypeSelector',
            'label' => 'label.pageType',
        ]);
    }
}
