<?php

namespace AppBundle\AdvancedFilters;

use AppBundle\Form\Type\AdvancedFilter\AdvancedFilterParameters;
use Common\Enum\AFSourceEnum;
use IndexBundle\Filter\FilterInterface;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Interface AFResolverInterface
 * Resolve concrete advanced filter.
 *
 * @package AppBundle\AdvancedFilters
 */
interface AFResolverInterface
{

    /**
     * Max advanced filters values per page for each filter.
     */
    const MAX_VALUES = 10;

    /**
     * Get Available values for specified filter or for all.
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     *
     * @return array
     */
    public function getAvailables(SearchRequestInterface $request);

    /**
     * Generate proper FilterInterface instance for specified filter name.
     *
     * @param array                    $AFConfig Advanced filters configuration.
     * @param string                   $name     Filter name.
     * @param AdvancedFilterParameters $params   Filter value or value label.
     *
     * @return FilterInterface
     */
    public function generateFilter(array $AFConfig, $name, AdvancedFilterParameters $params);
}
