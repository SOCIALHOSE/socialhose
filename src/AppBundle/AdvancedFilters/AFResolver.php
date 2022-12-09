<?php

namespace AppBundle\AdvancedFilters;

use AppBundle\AdvancedFilters\Aggregator\AFAggregatorInterface;
use AppBundle\Form\Type\AdvancedFilter\AdvancedFilterParameters;
use Common\Enum\AFTypeEnum;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Class AFResolver
 *
 * @package AppBundle\AdvancedFilters\Elasticsearch
 */
class AFResolver implements AFResolverInterface
{

    /**
     * @var AFAggregatorInterface
     */
    private $aggregator;

    /**
     * @var FilterFactoryInterface
     */
    private $factory;

    /**
     * AFResolver constructor.
     *
     * @param AFAggregatorInterface  $aggregator A AFAggregatorInterface
     *                                           instance.
     * @param FilterFactoryInterface $factory    A FilterFactoryInterface
     *                                           instance.
     */
    public function __construct(
        AFAggregatorInterface $aggregator,
        FilterFactoryInterface $factory
    ) {
        $this->aggregator = $aggregator;
        $this->factory = $factory;
    }

    /**
     * Get Available values for specified filter or for all.
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     *
     * @return array
     */
    public function getAvailables(SearchRequestInterface $request)
    {
        //
        // Return assoc array for all available filters with its values.
        //
        return array_map(function ($values) {
            return [ 'data' => $values ];
        }, $this->aggregator->getValues($request));
    }

    /**
     * Generate proper FilterInterface instance for specified filter name.
     *
     * @param array                    $AFConfig Advanced filters configuration.
     * @param string                   $name     Filter name.
     * @param AdvancedFilterParameters $params   Filter value or value label.
     *
     * @return \IndexBundle\Filter\FilterInterface
     */
    public function generateFilter(array $AFConfig, $name, AdvancedFilterParameters $params)
    {
        if (! isset($AFConfig[$name])) {
            throw new \InvalidArgumentException("Unknown filter '{$name}'.");
        }
        $config = $AFConfig[$name];
        $fieldName = $config['field_name'];

        switch ($config['type']) {
            //
            // Additional query.
            //
            case AFTypeEnum::QUERY:
                $filter = $params->createQueryFilter($config['names'], $this->factory);
                break;

            //
            // Filter by range.
            //
            case AFTypeEnum::RANGE:
                $ranges = $config['ranges'];
                $filter = $params->createRangeFilter($fieldName, $ranges, $this->factory);
                break;

            //
            // Filter by single value.
            //
            case AFTypeEnum::SIMPLE:
                //
                // NOTICE:
                //
                // We not validate given value so client may provide valid value
                // for current filtered field but not existing in available filter
                // values for current search request.
                //
                // In this case client will receive zero documents, so maybe
                // validating is not necessary.
                //
                $filter = $params->createSimpleFilter($fieldName, $this->factory);
                break;

            default:
                throw new \RuntimeException("Unsupported type {$config['type']}");
        }

        return $filter;
    }
}
