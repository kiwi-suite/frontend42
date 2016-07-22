<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Form\Sitemap;

use Frontend42\FormElements\PageTypeSelector;
use Zend\Form\Element\Text;
use Zend\Form\Form;

class CreateForm extends Form
{
    /**
     *
     */
    public function init()
    {
        $name = new Text("name");
        $name->setLabel("label.name");
        $name->setAttribute("required", "required");
        $this->add($name);
        
        /** @var PageTypeSelector $role */
        $pageTypeSelector = $this->getFormFactory()->getFormElementManager()->get('page_type_selector');
        $pageTypeSelector->setName("page_type_selector");
        $pageTypeSelector->setLabel("label.pageType");
        $pageTypeSelector->setAttribute("required", "required");
        $this->add($pageTypeSelector);

        $pageSelector = $this->getFormFactory()->getFormElementManager()->get('page_selector');
        $pageSelector->setName("page_selector");
        $pageSelector->setLabel("label.pageSelector");
        $pageSelector->setAttribute("required", "required");
        $this->add($pageSelector);
    }
}
