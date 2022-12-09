<?php

namespace IndexBundle\Filter\Factory;

use IndexBundle\Filter\FilterInterface;
use IndexBundle\Filter\Filters;
use IndexBundle\Filter\SingleFilterInterface;

/**
 * Class FilterFactory
 * @package IndexBundle\Filter\Factory
 *
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
class FilterFactory implements FilterFactoryInterface
{

    /**
     * Check that given document field contains value greater or equal to
     * specified.
     *
     * @param string $name  Filtered field name.
     * @param mixed  $value Filter value.
     *
     * @return Filters\GteFilter
     */
    public function gte($name, $value)
    {
        return new Filters\GteFilter($name, $value);
    }

    /**
     * Check that given document field contains value greater than specified.
     *
     * @param string $name  Filtered field name.
     * @param mixed  $value Filter value.
     *
     * @return Filters\GtFilter
     */
    public function gt($name, $value)
    {
        return new Filters\GtFilter($name, $value);
    }

    /**
     * Check that given document field value equal to specified.
     *
     * @param string $name  Filtered field name.
     * @param mixed  $value Filter value.
     *
     * @return Filters\EqFilter
     */
    public function eq($name, $value)
    {
        return new Filters\EqFilter($name, $value);
    }

    /**
     * Check that given document field contains value less than specified.
     *
     * @param string $name  Filtered field name.
     * @param mixed  $value Filter value.
     *
     * @return Filters\LtFilter
     */
    public function lt($name, $value)
    {
        return new Filters\LtFilter($name, $value);
    }

    /**
     * Check that given document field contains value less or equal to
     * specified.
     *
     * @param string $name  Filtered field name.
     * @param mixed  $value Filter value.
     *
     * @return Filters\LteFilter
     */
    public function lte($name, $value)
    {
        return new Filters\LteFilter($name, $value);
    }

    /**
     * Check that given document field value equal to one of specified values.
     *
     * @param string       $name   Filtered field name.
     * @param array|string $values Filter values.
     *
     * @return Filters\InFilter
     */
    public function in($name, $values)
    {
        return new Filters\InFilter($name, (array) $values);
    }

    /**
     * Check that given document field value match specified pattern.
     *
     * @param string $name    Filtered field name.
     * @param string $pattern Filter pattern.
     *
     * @return Filters\RegexpFilter
     */
    public function regex($name, $pattern)
    {
        return new Filters\RegexpFilter($name, $pattern);
    }

    /**
     * Reverse specified filter.
     *
     * @param SingleFilterInterface $filter A SingleFilterInterface instance.
     *
     * @return Filters\NotFilter
     */
    public function not(SingleFilterInterface $filter)
    {
        return new Filters\NotFilter($filter);
    }

    /**
     * Check that all specified filters are true.
     *
     * @param FilterInterface[]|FilterInterface $filters A FilterInterface
     *                                                   instance or array of
     *                                                   instances.
     *
     * @return Filters\AndFilter
     */
    public function andX($filters = null)
    {
        return new Filters\AndFilter($filters);
    }

    /**
     * Check that at least one of specified filters are true.
     *
     * @param FilterInterface[]|FilterInterface $filters A FilterInterface
     *                                                   instance or array of
     *                                                   instances.
     *
     * @return Filters\OrFilter
     */
    public function orX($filters = null)
    {
        return new Filters\OrFilter($filters);
    }
}
