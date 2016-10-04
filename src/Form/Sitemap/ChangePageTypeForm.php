<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Form\Sitemap;

use Zend\Form\Form;

class ChangePageTypeForm extends Form
{
    /**
     *
     */
    public function init()
    {
        $this->add([
            'name' => 'pageTypeSelector',
            'type' => 'pageTypeSelector',
            'label' => 'label.pageType',
            'required' => true,
        ]);
    }
}
