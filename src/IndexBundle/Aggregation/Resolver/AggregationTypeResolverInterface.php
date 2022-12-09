<?php

namespace IndexBundle\Aggregation\Resolver;

/**
 * Interface AggregationTypeResolverInterface
 * @package IndexBundle\Aggregation\Resolver
 */
interface AggregationTypeResolverInterface
{

    /**
     * Generate structure for need type of the aggregation
     *
     * @param string  $fieldName Aggregated field name.
     * @param integer $size      Aggregation size.
     *
     * @return array
     */
    public function terms($fieldName, $size);

    /**
     * Generate structure for need type of the aggregation
     *
     * @param integer  $size    Aggregation size.
     * @param string[] $sources Requested sources.
     *
     * @return array
     */
    public function topHits($size, array $sources);

    /**
     * Generate structure for range type of the aggregation
     *
     * @param string  $fieldName Aggregated field name.
     * @param array[] $ranges    Aggregation ranges.
     *
     * @return array
     */
    public function range($fieldName, array $ranges);

    /**
     * Generate structure for date_histogram type of the aggregation
     *
     * @param string $fieldName Aggregated field name.
     * @param string $interval  Histogram interval.
     *
     * @return array
     */
    public function dateHistogram($fieldName, $interval);
}
