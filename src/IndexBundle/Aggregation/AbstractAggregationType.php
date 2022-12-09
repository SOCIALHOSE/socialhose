<?php

namespace IndexBundle\Aggregation;

/**
 * Class AbstractAggregationType
 * @package IndexBundle\Aggregation
 */
abstract class AbstractAggregationType implements AggregationTypeInterface
{

    /**
     * @var integer
     */
    protected $size = 1;

    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var array[]
     */
    protected $ranges = [];

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
        $this->fieldName = array_key_exists('field_name', $settings)
                                            ? $settings['field_name'] : '';
        $this->size = array_key_exists('size', $settings) && is_numeric($settings['size'])
                                            ? $settings['size'] : null;
        $this->ranges = array_key_exists('ranges', $settings) && is_array($settings['ranges'])
                                            ? $settings['ranges'] : [];
    }
}
