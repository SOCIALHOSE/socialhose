<?php

namespace ApiDocBundle\Formatter;

use Nelmio\ApiDocBundle\Formatter\HtmlFormatter as BaseFormatter;

/**
 * Class AppHtmlFormatter
 * @package ApiDocBundle\Formatter
 */
class AppHtmlFormatter extends BaseFormatter
{

    /**
     * @param  array   $data                 Annotations.
     * @param  string  $parentName           Parent name.
     * @param  boolean $ignoreNestedReadOnly Flag.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function compressNestedParameters(
        array $data,
        $parentName = null,
        $ignoreNestedReadOnly = false
    ) {
        $newParams = [];
        foreach ($data as $name => $info) {
            $format = $this->getParameters($info, 'format');
            $choices = [];
            if ($format !== null) {
                $choices = json_decode($format, true);
                if ($choices === null) {
                    $choices = [];
                }
            }

            $newParams[$name] = [
                'dataType'     => $info['dataType'],
                'readonly'     => $this->getParameters($info, 'readonly'),
                'required'     => $this->getParameters($info, 'required', true),
                'default'      => $this->getParameters($info, 'default'),
                'description'  => $this->getParameters($info, 'description'),
                'format'       => $format,
                'sinceVersion' => $this->getParameters($info, 'sinceVersion'),
                'untilVersion' => $this->getParameters($info, 'untilVersion'),
                'actualType'   => $this->getParameters($info, 'actualType'),
                'subType'      => $this->getParameters($info, 'subType'),
                'choices'      => $choices,
            ];

            if (isset($info['children']) && (!$info['readonly'] || !$ignoreNestedReadOnly)) {
                foreach ($this->compressNestedParameters($info['children'], $name, $ignoreNestedReadOnly) as $nestedItemName => $nestedItemData) {
                    $newParams[$name]['children'][$nestedItemName] = $nestedItemData;
                }
            }
        }

        return $newParams;
    }

    /**
     * @param array|mixed $annotation A normalized ApiDoc Annotation.
     *
     * @return array
     */
    protected function processAnnotation($annotation)
    {
        $result = parent::processAnnotation($annotation);

        // Sort status codes.
        if (isset($result['statusCodes'])) {
            ksort($result['statusCodes']);
        }

        return $result;
    }

    /**
     * @param array  $info      Info array.
     * @param string $parameter Parameter name.
     * @param mixed  $default   Default value if parameter not found.
     *
     * @return mixed
     */
    private function getParameters(array $info, $parameter, $default = null)
    {
        return array_key_exists($parameter, $info) ? $info[$parameter] : $default;
    }
}
