<?php

namespace IndexBundle\Filter;

/**
 * Interface SingleFilterInterface
 * @package IndexBundle\Filter
 */
interface SingleFilterInterface extends FilterInterface
{

    /**
     * Get filtered field name.
     *
     * @return string
     */
    public function getFieldName();

    /**
     * Get filter value.
     *
     * @return mixed
     */
    public function getValue();
}
