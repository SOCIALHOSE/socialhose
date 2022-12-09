<?php

namespace IndexBundle\Filter\Filters;

use IndexBundle\Filter\AbstractValueFilter;
use IndexBundle\Filter\Resolver\FilterResolverInterface;

/**
 * Class EqFilter
 * @package IndexBundle\Filter\Filters
 */
class EqFilter extends AbstractValueFilter
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
        return $resolver->eq($this->field, $this->value);
    }
}
