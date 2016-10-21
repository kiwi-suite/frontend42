<?php
namespace Frontend42\Form;

use Admin42\FormElements\Fieldset;
use Admin42\FormElements\Form;
use Zend\Form\FormInterface;

class EditPageForm extends Form
{
    /**
     *
     */
    public function init()
    {
        $this->add([
            'name' => 'csrf',
            'type' => 'csrf',
            'csrfOptions' => [
                'timeout' => 14400,
            ],
        ]);
    }

    /**
     * @param array $sections
     */
    public function addPageElements(array $sections)
    {
        foreach ($sections as $key => $section) {
            if (empty($section['elements'])) {
                continue;
            }

            $fieldset = $this->getFormFactory()->getFormElementManager()->get(Fieldset::class);
            $fieldset->setName('section' . $key);
            if (!empty($section['label'])) {
                $fieldset->setLabel($section['label']);
            }

            foreach ($section['elements'] as $element) {
                $fieldset->add($element);
            }

            $this->add($fieldset);
        }
    }

    /**
     * @param int $flag
     * @return array
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {
        $data = parent::getData($flag);

        $newData = [];
        foreach ($data as $name => $fieldset) {
            if (substr($name, 0 ,7) !== 'section') {
                continue;
            }
            $newData = array_merge($newData, $fieldset);
        }

        return $newData;
    }

    /**
     * @param array $sections
     * @param array $data
     */
    public function setDatabaseData(array $sections, array $data)
    {
        $newData = [];
        foreach ($sections as $key => $section) {
            if (empty($section['elements'])) {
                continue;
            }
            $name = 'section' . $key;
            foreach ($section['elements'] as $element) {
                if (empty($element['name'])) {
                    continue;
                }
                if (!array_key_exists($element['name'], $data)) {
                    continue;
                }

                $newData[$name][$element['name']] = $data[$element['name']];
            }
        }

        $this->setData($newData);
    }
}
