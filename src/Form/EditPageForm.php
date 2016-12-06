<?php
namespace Frontend42\Form;

use Admin42\FormElements\Fieldset;
use Admin42\FormElements\Form;
use Admin42\FormElements\StrategyAwareInterface;
use Core42\Hydrator\Strategy\StringStrategy;
use Core42\Model\GenericModel;

class EditPageForm extends Form
{
    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var array
     */
    protected $sections = [];

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
     * @param array $defaults
     * @return $this
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * @param array $sections
     * @return $this
     */
    public function setSections(array $sections)
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     *
     */
    public function addPageElements()
    {
        foreach ($this->sections as $key => $section) {
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

    protected function getDefaults()
    {
        $defaults = [];
        foreach ($this->defaults as $defaultSpec) {
            if (empty($defaultSpec['name'])) {
                continue;
            }
            if (empty($defaultSpec['type'])) {
                continue;
            }
            if (!array_key_exists("value", $defaultSpec)) {
                continue;
            }
            foreach ($this->sections as $section) {
                foreach ($section['elements'] as $element) {
                    if (empty($element['name'])) {
                        continue;
                    }

                    if ($element['name'] === $defaultSpec['name']) {
                        continue 2;
                    }
                }
            }

            $defaults[] = $defaultSpec;
        }

        return $defaults;
    }

    /**
     * @return array
     */
    public function getDataForDatabase()
    {
        $newData = [];

        $hydrator = clone $this->hydratorPrototype;
        $strategies = [];
        $defaultData = [];

        foreach ($this->getDefaults() as $default) {
            $element = $this->getFormFactory()->getFormElementManager()->get($default['type']);
            $strategy = StringStrategy::class;
            if ($element instanceof StrategyAwareInterface) {
                $strategy = $element->getStrategy();
            }
            $strategies[] = $strategy;

            $defaultData[$default['name']] = $default['value'];
        }

        if (!empty($defaultData)) {
            $hydrator->addStrategies($strategies);
            $model = $hydrator->hydrate($defaultData, new GenericModel());
            $newData = $model->toArray();
        }

        $data = $this->getData();
        foreach ($data as $name => $fieldset) {
            if (substr($name, 0 ,7) !== 'section') {
                continue;
            }
            $newData = array_merge($newData, $fieldset);
        }

        return $newData;
    }

    /**
     * @param array $data
     */
    public function setDatabaseData(array $data)
    {
        $newData = [];
        foreach ($this->sections as $key => $section) {
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
