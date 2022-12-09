<?php

namespace AppBundle\AdvancedFilters\Aggregator;

use AppBundle\AdvancedFilters\AdvancedFiltersConfig;
use Common\Enum\AFSourceEnum;

/**
 * Class ArticleAFAggregator
 *
 * @package AppBundle\AdvancedFilters\Aggregator
 */
class ArticleAFAggregator extends AbstractElasticSearchAFAggregator
{

    /**
     * Aggregation config.
     *
     * @return array
     */
    protected function getAggregationConfig()
    {
        return AdvancedFiltersConfig::getConfig(AFSourceEnum::FEED);
    }

    /**
     * Get default value for this aggregation results.
     *
     * @return array
     */
    protected function getDefaultValue()
    {
        return AdvancedFiltersConfig::getDefault(AFSourceEnum::FEED);
    }
}
