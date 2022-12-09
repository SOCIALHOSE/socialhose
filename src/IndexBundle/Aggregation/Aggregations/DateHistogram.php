<?php

namespace IndexBundle\Aggregation\Aggregations;

use IndexBundle\Aggregation\AbstractAggregationType;
use IndexBundle\Aggregation\Resolver\AggregationTypeResolverInterface;

/**
 * Class DateHistogram
 * @package IndexBundle\Aggregation\Aggregations
 */
class DateHistogram extends AbstractAggregationType
{
    /**
     * @var string
     */
    protected $interval = '';

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
        $this->interval = array_key_exists('interval', $settings) && is_string($settings['interval'])
            ? $settings['interval'] : '';

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
        return $resolver->dateHistogram($this->fieldName, $this->interval);
    }
}
