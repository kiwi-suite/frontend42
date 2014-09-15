<?php
namespace Frontend42\I18n\Locale;

use Zend\Stdlib\AbstractOptions;

class LocaleOptions extends AbstractOptions
{
    protected $default;

    protected $list = array();

    protected $languageSelection = false;

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return boolean|array
     */
    public function getLanguageSelection()
    {
        return $this->languageSelection;
    }

    /**
     * @param boolean|array $languageSelection
     */
    public function setLanguageSelection($languageSelection)
    {
        $this->languageSelection = $languageSelection;
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param array $list
     */
    public function setList($list)
    {
        $this->list = $list;
    }


}
