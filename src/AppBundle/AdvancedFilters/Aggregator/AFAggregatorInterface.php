<?php

namespace AppBundle\AdvancedFilters\Aggregator;

use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Interface AFAggregatorInterface
 *
 * Advanced filters aggregator interface.
 *
 * @package AppBundle\AdvancedFilters\Aggregator
 */
interface AFAggregatorInterface
{

    /**
     * Return available filters values for specified request.
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     *
     * @return array
     */
    public function getValues(SearchRequestInterface $request);
}
