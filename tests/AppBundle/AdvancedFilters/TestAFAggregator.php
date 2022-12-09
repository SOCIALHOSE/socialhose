<?php

namespace Tests\AppBundle\AdvancedFilters;

use AppBundle\AdvancedFilters\Aggregator\AFAggregatorInterface;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Class TestAFAggregator
 * @package AppBundle\AdvancedFilters
 */
class TestAFAggregator implements AFAggregatorInterface
{

    /**
     * @var array
     */
    private $values;

    /**
     * TestAFAggregator constructor.
     *
     * @param array $values Aggregator values.
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * Return available filters values for specified request.
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     *
     * @return array
     */
    public function getValues(SearchRequestInterface $request)
    {
        return $this->values;
    }
}
