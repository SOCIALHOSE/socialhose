<?php

namespace IndexBundle\Aggregation\Aggregations;

use IndexBundle\Aggregation\AbstractAggregationType;
use IndexBundle\Aggregation\Resolver\AggregationTypeResolverInterface;

/**
 * Class RangeAggregation
 * @package IndexBundle\Aggregation\Aggregations
 */
class RangeAggregation extends AbstractAggregationType
{

    /**
     * Resolve given aggregation into proper index format.
     *
     * @param AggregationTypeResolverInterface $resolver A AggregationTypeResolverInterface
     *                                                   instance.
     *
     * @return mixed
     */
    public function resolve(AggregationTypeResolverInterface $resolver)
    {
        return $resolver->range($this->fieldName, $this->ranges);
    }
}
