<?php
namespace Frontend42\PageType;

class PageTypeContent
{
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
     * @param $rawContent
     */
    public function setFromFormData($rawContent)
    {
        $elementArr = array_values($rawContent);

        $this->content = [];

        foreach ($elementArr as $array) {
            foreach ($array as $name => $value) {
                $this->content[$name] = $value;
            }
        }
    }

    /**
     * @param array $formOptions
     * @param array $content
     * @return array
     */
    public function generateFormData(array $formOptions, array $content)
    {
        $formContent = [];
        foreach ($formOptions as $section => $sectionData) {
            foreach ($sectionData['elements'] as $formName) {
                if (!array_key_exists($formName, $content)) {
                    continue;
                }

                $formContent[$section][$formName] = $content[$formName];
            }
        }
        return $formContent;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        if (array_key_exists($name, $this->content)) {
            return $this->content[$name];
        }

        return $default;
    }
}
