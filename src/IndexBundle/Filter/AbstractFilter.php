<?php

namespace IndexBundle\Filter;

/**
 * Class AbstractFilter
 * Base class for all filters.
 *
 * @package IndexBundle\Filter
 */
abstract class AbstractFilter implements FilterInterface
{

    /**
     * Return type of current filter. Used in serialization.
     *
     * @return string
     * @deprecated
     */
    protected function getType()
    {
        $class = static::class;
        $type = substr($class, strrpos($class, '\\') + 1);

        return strtolower(str_replace('Filter', '', $type));
    }
}
