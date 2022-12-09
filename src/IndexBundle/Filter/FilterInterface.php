<?php

namespace IndexBundle\Filter;

use IndexBundle\Filter\Resolver\FilterResolverInterface;

/**
 * Interface FilterInterface
 * @package IndexBundle\Filter
 */
interface FilterInterface extends \Serializable
{

    /**
     * Resolve given filter into proper index format.
     *
     * @param FilterResolverInterface $resolver A resolver instance used for resolving
     *                                          this filter.
     *
     * @return mixed
     */
    public function resolve(FilterResolverInterface $resolver);

    /**
     * Sort filter values or internal filters.
     *
     * @return void
     */
    public function sort();
}
