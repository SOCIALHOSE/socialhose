<?php

namespace IndexBundle\Index\Source;

use AppBundle\AdvancedFilters\Aggregator\AFAggregatorInterface;
use AppBundle\AdvancedFilters\Aggregator\SourceAFAggregator;
use IndexBundle\Index\Internal\InternalIndex;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Index\Strategy\SourceIndexStrategy;

/**
 * Class SourceIndex
 *
 * @package IndexBundle\Index\Source
 */
class SourceIndex extends InternalIndex implements SourceIndexInterface
{

    /**
     * Max filters values per page for each filter.
     */
    const MAX_VALUES = 10;

    /**
     * Create concrete strategy instance.
     *
     * @return IndexStrategyInterface
     */
    protected function createStrategy()
    {
        return new SourceIndexStrategy();
    }

    /**
     * @return AFAggregatorInterface
     */
    protected function createAggregator()
    {
        return new SourceAFAggregator($this);
    }

    /**
     * @param array $normalized Normalized document data.
     * @param array $raw        Raw document data.
     *
     * @return array
     */
    protected function additionalNormalization(array $normalized, array $raw)
    {
        if (! isset($normalized['id'])) {
            //
            // Use id provided by Elastic if hose not return sequence field.
            // Just in case.
            //
            $normalized['id'] = $raw['_id'];
        }

        return $normalized;
    }
}
