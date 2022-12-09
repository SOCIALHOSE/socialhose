<?php

namespace IndexBundle\Filter\Filters;

use IndexBundle\Filter\AbstractGroupFilter;
use IndexBundle\Filter\Resolver\FilterResolverInterface;

/**
 * Class AndFilter
 * @package IndexBundle\Filter\Filters
 */
class AndFilter extends AbstractGroupFilter
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
        return $resolver->andX($this->filters);
    }
}
