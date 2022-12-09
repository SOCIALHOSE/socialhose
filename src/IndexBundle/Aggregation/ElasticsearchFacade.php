<?php

namespace IndexBundle\Aggregation;

/**
 * Class ElasticsearchFacade
 * @package IndexBundle\Aggregation
 */
class ElasticsearchFacade implements AggregationFacadeInterface
{

    /**
     * Get need aggregation realization.
     *
     * @param string                   $name Aggregation name.
     * @param AggregationTypeInterface $type Aggregation type.
     *
     * @return AggregationInterface
     */
    public function getAggregation($name, AggregationTypeInterface $type)
    {
        return new ElasticsearchAggregation($type, $name);
    }
}
