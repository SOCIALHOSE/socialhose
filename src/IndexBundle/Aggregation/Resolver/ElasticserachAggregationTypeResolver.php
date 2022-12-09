<?php

namespace IndexBundle\Aggregation\Resolver;

use IndexBundle\Index\Strategy\IndexStrategyInterface;

/**
 * Class ElasticserachAggregationTypeResolver
 * @package IndexBundle\Aggregation\Resolver
 */
class ElasticserachAggregationTypeResolver implements AggregationTypeResolverInterface
{

    /**
     * @var IndexStrategyInterface
     */
    private $strategy;

    /**
     * ElasticserachAggregationTypeResolver constructor.
     *
     * @param IndexStrategyInterface $strategy A IndexStrategyInterface instance.
     */
    public function __construct(IndexStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Generate structure for need type of the aggregation
     *
     * @param string  $fieldName Aggregated field name.
     * @param integer $size      Aggregation size.
     *
     * @return array
     */
    public function terms($fieldName, $size)
    {
        $params = [ 'field' => $this->strategy->denormalizeFieldName($fieldName, true) ];

        if ($size !== null) {
            $params['size'] = $size;
        }
        return [ 'terms' => $params ];
    }

    /**
     * Generate structure for need type of the aggregation
     *
     * @param integer  $size    Aggregation size.
     * @param string[] $sources Requested sources.
     *
     * @return array
     */
    public function topHits($size, array $sources)
    {
        $config = [
            'size' => $size,
        ];

        if (count($sources) > 0) {
            $config['_source'] = \nspl\a\map(function ($source) {
                return $this->strategy->denormalizeFieldName($source);
            }, $sources);
        }

        return [ 'top_hits' => $config ];
    }

    /**
     * Generate structure for range type of the aggregation
     *
     * @param string  $fieldName Aggregated field name.
     * @param array[] $ranges    Aggregation ranges.
     *
     * @return array
     */
    public function range($fieldName, array $ranges)
    {
        return [
            'range' => [
                'field' => $this->strategy->denormalizeFieldName($fieldName, true),
                'ranges' => $ranges,
            ],
        ];
    }

    /**
     * Generate structure for date_histogram type of the aggregation
     *
     * @param string $fieldName Aggregated field name.
     * @param string $interval  Histogram interval.
     *
     * @return array
     */
    public function dateHistogram($fieldName, $interval)
    {
        return [
            'date_histogram' => [
                'field' => $this->strategy->denormalizeFieldName($fieldName, true),
                'interval' => $interval,
            ],
        ];
    }
}
