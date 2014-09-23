<?php
namespace Frontend42\Page;

use Frontend42\Form\PageAddForm;
use Zend\Form\Form;

interface PageInterface
{
    public function saveInitForm(PageAddForm $form, $locale);

    public function getEditForm($id, $locale);

    public function saveEditForm($data, $id, $locale);
}
