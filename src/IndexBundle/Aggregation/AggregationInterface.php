<?php

namespace IndexBundle\Aggregation;

use IndexBundle\Aggregation\Resolver\AggregationTypeResolverInterface;

/**
 * Interface AggregationInterface
 * @package IndexBundle\Aggregation
 */
interface AggregationInterface
{

    /**
     * Overwrite existence aggregations
     *
     * @param AggregationInterface|AggregationInterface[] $aggregations An AggregationInterface instance
     *                                                                  or array of
     *                                                                  AggregationInterface instances.
     *
     * @return AggregationInterface
     */
    public function setAggregations($aggregations);

    /**
     * Add new aggregation
     *
     * @param AggregationInterface $aggregation A AggregationInterface instance.
     *
     * @return AggregationInterface
     */
    public function addAggregation(AggregationInterface $aggregation);

    /**
     * Resolve aggregation to need format to query.
     *
     * @param AggregationTypeResolverInterface $resolver A AggregationTypeResolverInterface
     *                                                   instance.
     *
     * @return mixed
     */
    public function build(AggregationTypeResolverInterface $resolver);
}
