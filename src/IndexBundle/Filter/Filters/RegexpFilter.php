<?php

namespace IndexBundle\Filter\Filters;

use IndexBundle\Filter\AbstractValueFilter;
use IndexBundle\Filter\Resolver\FilterResolverInterface;

/**
 * Class RegexpFilter
 * @package IndexBundle\Filter\Filters
 */
class RegexpFilter extends AbstractValueFilter
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
        return $resolver->regex($this->field, $this->value);
    }
}
