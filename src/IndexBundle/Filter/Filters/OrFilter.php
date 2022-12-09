<?php

namespace IndexBundle\Filter\Filters;

use IndexBundle\Filter\AbstractGroupFilter;
use IndexBundle\Filter\Resolver\FilterResolverInterface;

/**
 * Class OrFilter
 * @package IndexBundle\Filter\Filters
 */
class OrFilter extends AbstractGroupFilter
{

    /**
     * Resolve given filter into proper index format.
     *
     * @param FilterResolverInterface $resolver A resolver instance used for resolving
     *                                          this filter.
     *
     * @return mixed
     */
    public function resolve(FilterResolverInterface $resolver)
    {
        return $resolver->orX($this->filters);
    }
}
