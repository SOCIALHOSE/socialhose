<?php

namespace IndexBundle\Aggregation\Factory;

use IndexBundle\Aggregation\Aggregations\DateHistogram;
use IndexBundle\Aggregation\Aggregations\RangeAggregation;
use IndexBundle\Aggregation\Aggregations\TermsAggregation;
use IndexBundle\Aggregation\Aggregations\TopHitsAggregation;

/**
 * Class AggregationFactory
 * @package IndexBundle\Aggregation\Factory
 */
class AggregationFactory implements AggregationFactoryInterface
{

    /**
     * Terms uses for grouping and computing number of document in each
     * group(like COUNT and GROUP BY in SQL)
     *
     * @param array $settings Terms aggregation settings.
     *
     * @return TermsAggregation
     */
    public function terms(array $settings)
    {
        return new TermsAggregation($settings);
    }

    /**
     * top_hits aggregation type
     *
     * @param array $settings Top hits aggregation settings.
     *
     * @return TopHitsAggregation
     */
    public function topHits(array $settings)
    {
        return new TopHitsAggregation($settings);
    }

    /**
     * ranges aggregation type
     *
     * @param array $settings Range aggregation setting.
     *
     * @return RangeAggregation
     */
    public function range(array $settings)
    {
        return new RangeAggregation($settings);
    }

    /**
     * Date Histogram aggregation type
     *
     * @param array $settings Date histogram aggregation settings.
     *
     * @return DateHistogram
     */
    public function dateHistogram(array $settings)
    {
        return new DateHistogram($settings);
    }
}
