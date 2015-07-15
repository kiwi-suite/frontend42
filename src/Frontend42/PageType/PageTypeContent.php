<?php
namespace Frontend42\PageType;

class PageTypeContent
{
    /**
     * @var array
     */
    protected $rawContent;

    /**
     * @param array $rawContent
     * @return $this
     */
    public function setRawContent(array $rawContent)
    {
        $this->rawContent = $rawContent;

        return $this;
    }

    /**
     * @return array
     */
    public function getRawContent()
    {
        return $this->rawContent;
    }

    /**
     * @param string $element
     * @param mixed $default
     * @return mixed
     */
    public function getElement($element, $default = null)
    {
        $elementArr = array_values($this->rawContent);

        foreach($elementArr as $array) {
            if (isset($array[$element])) {
                return $array[$element];
            }
        }

        return $default;
    }
}
