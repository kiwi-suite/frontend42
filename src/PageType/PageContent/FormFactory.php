<?php
namespace Frontend42\PageType\PageContent;

abstract class FormFactory
{
    /**
     * @param array $sections
     * @param array $data
     * @return array
     */
    public static function fromForm(array $sections, array $data)
    {
        $result = [];
        foreach ($sections as $fieldsetName => $section) {
            $fieldsetName = "fieldset" . $fieldsetName;
            if (empty($data[$fieldsetName])) {
                continue;
            }

            if (empty($section['elements'])) {
                continue;
            }

            foreach ($section['elements'] as $element) {
                if (empty($element['name'])) {
                    continue;
                }

                if (empty($data[$fieldsetName][$element['name']])) {
                    continue;
                }

                $result[$element['name']] = $data[$fieldsetName][$element['name']];
            }
        }

        return $result;
    }

    /**
     * @param array $sections
     * @param array $data
     * @return array
     */
    public static function toForm(array $sections, array $data)
    {
        $result = [];

        foreach ($sections as $fieldsetName => $section) {
            $fieldsetName = "fieldset" . $fieldsetName;
            $result[$fieldsetName] = [];

            if (empty($section['elements'])) {
                continue;
            }

            foreach ($section['elements'] as $element) {
                if (empty($element['name'])) {
                    continue;
                }

                if (empty($data[$element['name']])) {
                    continue;
                }

                $result[$fieldsetName][$element['name']] = $data[$element['name']];
            }
        }

        return $result;
    }
}
