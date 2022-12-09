<?php

namespace IndexBundle\Aggregation;

/**
 * Interface AggregationFacadeInterface
 * @package IndexBundle\Aggregation
 */
interface AggregationFacadeInterface
{

    /**
     * Get need aggregation realization.
     *
     * @param string                   $name Aggregation name.
     * @param AggregationTypeInterface $type Aggregation type.
     *
     * @return AggregationInterface
     */
    public function getAggregation($name, AggregationTypeInterface $type);
}
