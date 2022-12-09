<?php

namespace IndexBundle\Filter;

/**
 * Interface GroupFilterInterface
 * @package IndexBundle\Filter
 */
interface GroupFilterInterface extends FilterInterface, \Countable
{

    /**
     * Add new filter to group.
     *
     * @param FilterInterface $filter A FilterInterface instance.
     *
     * @return GroupFilterInterface
     */
    public function add(FilterInterface $filter);

    /**
     * Get all internal filters.
     *
     * @return FilterInterface[]
     */
    public function getFilters();

    /**
     * Set internal filters.
     *
     * @param FilterInterface[]|array $filters Array of FilterInterface instances.
     *
     * @return static
     */
    public function setFilters(array $filters);
}
