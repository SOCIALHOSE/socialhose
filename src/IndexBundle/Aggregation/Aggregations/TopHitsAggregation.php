<?php

namespace IndexBundle\Aggregation\Aggregations;

use IndexBundle\Aggregation\AbstractAggregationType;
use IndexBundle\Aggregation\Resolver\AggregationTypeResolverInterface;

/**
 * Class TopHitsAggregation
 * @package IndexBundle\Aggregation\Aggregations
 */
class TopHitsAggregation extends AbstractAggregationType
{

    /**
     * @var string[]
     */
    protected $sources = [];

    /**
     * $settings['field_name'] - See elastic search docs
     * $settings['size']       - See elastic search docs
     * $settings['sources']    - See elastic search docs
     * $settings['ranges']     - See elastic search docs(Range aggregation type)
     * sample
     *  "range" => [
     *      ['from' => 1, 'to' => 100],
     *      ['from' => 100, 'to' => 200],
     *      ...
     *  ]
     * $settings['interval']   - See elastic search docs(Date Histogram Aggregation)
     *
     * @param array $settings Aggregation settings.
     */
    public function __construct(array $settings)
    {
        $this->sources = array_key_exists('sources', $settings) && is_array($settings['sources'])
            ? $settings['sources'] : [];

        parent::__construct($settings);
    }

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
        return $resolver->topHits($this->size, $this->sources);
    }
}
