<?php

namespace IndexBundle\Filter\Resolver;

use IndexBundle\Filter\FilterInterface;

/**
 * Interface FilterResolverInterface
 * Resolve abstract filters into index specific
 *
 * @package IndexBundle\Filter\Resolver
 *
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
interface FilterResolverInterface
{

    /**
     * Generate 'greater or equal to' filter for search request.
     *
     * @param string $field Filtered filed name.
     * @param string $value Filter value.
     *
     * @return mixed
     */
    public function gte($field, $value);

    /**
     * Generate 'greater then' filter for search request.
     *
     * @param string $field Filtered filed name.
     * @param string $value Filter value.
     *
     * @return mixed
     */
    public function gt($field, $value);

    /**
     * Generate 'equal to' filter for search request.
     *
     * @param string $field Filtered filed name.
     * @param string $value Filter value.
     *
     * @return mixed
     */
    public function eq($field, $value);

    /**
     * Generate 'less then' filter for search request.
     *
     * @param string $field Filtered filed name.
     * @param string $value Filter value.
     *
     * @return mixed
     */
    public function lt($field, $value);

    /**
     * Generate 'less or equal to' filter for search request.
     *
     * @param string $field Filtered filed name.
     * @param string $value Filter value.
     *
     * @return mixed
     */
    public function lte($field, $value);

    /**
     * Generate 'in' filter for search request.
     *
     * @param string $field  Filtered filed name.
     * @param array  $values Filter values.
     *
     * @return mixed
     */
    public function in($field, array $values);

    /**
     * Generate regexp filter for search request.
     *
     * @param string $field   Filtered field name.
     * @param string $pattern Regexp pattern.
     *
     * @return mixed
     */
    public function regex($field, $pattern);

    /**
     * Generate 'not' filter for search request.
     *
     * @param FilterInterface $filter A FilterInterface instance.
     *
     * @return mixed
     */
    public function not(FilterInterface $filter);

    /**
     * Generate 'and' filter for search request.
     *
     * @param FilterInterface|FilterInterface[] $filters A FilterInterface
     *                                                   instance or array of
     *                                                   instances.
     *
     * @return mixed
     */
    public function andX($filters);

    /**
     * Generate 'or' filter for search request.
     *
     * @param FilterInterface|FilterInterface[] $filters A FilterInterface
     *                                                   instance or array of
     *                                                   instances.
     *
     * @return mixed
     */
    public function orX($filters);
}
