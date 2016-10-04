<?php
namespace Frontend42\PageType\Provider;

use Zend\Stdlib\Glob;

class PageTypeConfigProvider
{
    /**
     * @var array
     */
    protected $paths = [];

    /**
     * @param array $paths
     */
    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getPageTypeOptions()
    {
        $pageTypeOptions = [];

        foreach ($this->paths as $path) {
            $entries = Glob::glob($path);
            foreach ($entries as $file) {
                $options = require $file;

                $pageTypeName = pathinfo($file, PATHINFO_FILENAME);
                $options['name'] = $pageTypeName;

                foreach (['label', 'class'] as $check) {
                    if (empty($options[$check])) {
                        throw new \Exception(sprintf(
                            "No config parameter '%s' found in '%s'",
                            $check,
                            $file
                        ));
                    }
                }

                $pageTypeOptions[$pageTypeName] = $options;
            }
        }

        return $pageTypeOptions;
    }
}
