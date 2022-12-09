<?php

namespace IndexBundle\Aggregation;

use IndexBundle\Aggregation\Resolver\AggregationTypeResolverInterface;

/**
 * Interface AggregationTypeInterface
 * @package IndexBundle\Aggregation
 */
interface AggregationTypeInterface
{

    /**
     * Resolve given aggregation into proper index format.
     *
     * @param AggregationTypeResolverInterface $resolver A AggregationTypeResolverInterface
     *                                                   instance.
     *
     * @return mixed
     */
    public function resolve(AggregationTypeResolverInterface $resolver);
}
