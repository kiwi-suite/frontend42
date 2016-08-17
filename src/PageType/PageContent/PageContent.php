<?php
namespace Frontend42\PageType\PageContent;

class PageContent
{
    /**
     * @var array
     */
    protected $formDefinition = [];

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * PageContent constructor.
     * @param array $formDefinition
     * @param array $elements
     */
    public function __construct(array $formDefinition, array $elements)
    {
        $this->formDefinition = $formDefinition;
        $this->elements = $elements;
    }

    /**
     * @var array
     */
    protected $content;

    /**
     * @param array $content
     * @return $this
     */
    public function setContent(array $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        if ($this->hasParam($name)) {
            return $this->content[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setParam($name, $value)
    {
        $this->content[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasParam($name)
    {
        return array_key_exists($name, $this->content);
    }

    /**
     * @return array
     */
    public function generateFormData()
    {
        $formContent = [];
        foreach ($this->formDefinition as $sectionHandle => $subFormInfo) {
            foreach ($subFormInfo['elements'] as $element) {
                if (empty($this->elements[$element])) {
                    continue;
                }
                $formContent[$sectionHandle][$element] = $this->getParam($element);
            }
        }

        return $formContent;
    }

    /**
     * @param array $rawContent
     * @return array
     */
    public function setFromFormData(array $rawContent)
    {
        foreach ($this->formDefinition as $sectionHandle => $subFormInfo) {
            foreach ($subFormInfo['elements'] as $element) {
                if (empty($this->elements[$element])) {
                    continue;
                }

                $value = null;
                if (!empty($rawContent[$sectionHandle][$element])) {
                    $value = $rawContent[$sectionHandle][$element];
                }

                $this->content[$element] = $value;
            }
        }

        return $this->getContent();
    }
}
