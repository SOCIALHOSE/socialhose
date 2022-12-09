<?php

namespace AppBundle\AdvancedFilters\Aggregator;

use AppBundle\AdvancedFilters\AdvancedFiltersConfig;
use Common\Enum\AFSourceEnum;
use Common\Enum\DocumentsAFNameEnum;
use Common\Enum\AFTypeEnum;
use IndexBundle\Aggregation\AggregationInterface;
use IndexBundle\Index\IndexInterface;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Class SourceAFAggregator
 *
 * @package AppBundle\AdvancedFilters\Aggregator
 */
class SourceAFAggregator extends AbstractElasticSearchAFAggregator
{

    /**
     * Aggregation config.
     *
     * @return array
     */
    protected function getAggregationConfig()
    {
        return AdvancedFiltersConfig::getConfig(AFSourceEnum::SOURCE);
    }

    /**
     * Get default value for this aggregation results.
     *
     * @return array
     */
    protected function getDefaultValue()
    {
        return AdvancedFiltersConfig::getDefault(AFSourceEnum::SOURCE);
    }
}
