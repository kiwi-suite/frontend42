<?php
namespace Frontend42\PageType\PageContent;

class PageContent
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
}
