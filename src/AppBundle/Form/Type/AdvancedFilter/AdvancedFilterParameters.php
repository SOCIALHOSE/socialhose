<?php

namespace AppBundle\Form\Type\AdvancedFilter;

use IndexBundle\Filter\Factory\FilterFactoryInterface;
use IndexBundle\Filter\FilterInterface;

/**
 * Class AdvancedFilterParameters
 * @package AppBundle\Form\Type\AdvancedFilter
 */
class AdvancedFilterParameters
{

    /**
     * Array of values which MUST be present in the search result.
     *
     * @var array
     */
    private $included;

    /**
     * Array of values which MUST NOT be present in the search result.
     *
     * @var string[]
     */
    private $excluded;

    /**
     * AdvancedFilterParameters constructor.
     *
     * @param string|string[] $included Array of values which MUST be present in
     *                                  the search result.
     * @param string|string[] $excluded Array of values which MUST NOT be present
     *                                  in the search result.
     */
    public function __construct($included, $excluded)
    {
        $this->included = (array) $included;
        $this->excluded = (array) $excluded;
    }

    /**
     * @param string $value Additional query.
     *
     * @return AdvancedFilterParameters
     */
    public static function queryFilterParameters($value)
    {
        // @codingStandardsIgnoreStart
        return new static([ $value ], []);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get array of values which MUST be present in the search result.
     *
     * @return string[]
     */
    public function getIncluded()
    {
        return $this->included;
    }

    /**
     * Get array of values which MUST NOT be present in the search result.
     *
     * @return string[]
     */
    public function getExcluded()
    {
        return $this->excluded;
    }

    /**
     * @param array                  $names   Array of used field names.
     * @param FilterFactoryInterface $factory A FilterFactoryInterface instance.
     *
     * @return FilterInterface
     */
    public function createQueryFilter(array $names, FilterFactoryInterface $factory)
    {
        return $factory->orX(array_map(function ($name) use ($factory) {
            return $factory->eq($name, current($this->included));
        }, $names));
    }

    /**
     * Create filter for range advanced filter.
     *
     * @param string                 $name    A Field name.
     * @param array                  $ranges  Available field ranges.
     * @param FilterFactoryInterface $factory A FilterFactoryInterface instance.
     *
     * @return FilterInterface
     */
    public function createRangeFilter($name, array $ranges, FilterFactoryInterface $factory)
    {
        $availables = array_keys($ranges);
        (($value = current($this->included)) !== false) || ($value = current($this->excluded));

        if (! in_array($value, $availables, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid value \'%s\'. Expects one of %s.',
                $value,
                implode(', ', $availables)
            ));
        }

        //
        // Get start and end bound for given value.
        //
        $start = $ranges[$value]['from'];
        $end = isset($ranges[$value]['to']) ? $ranges[$value]['to'] : null;

        // Firstly create 'gte' filter.
        $filter = $factory->gte($name, $start);

        if ($end !== null) {
            //
            // We have end bound, so we should use 'lte' filter and wrap
            // both into 'andX'.
            //
            $filter = $factory->andX([
                $filter,
                $factory->lte($name, $end),
            ]);
        }

        return $filter;
    }

    /**
     * Create filter for simple advanced filter.
     *
     * @param string                 $name    A Field name.
     * @param FilterFactoryInterface $factory A FilterFactoryInterface instance.
     *
     * @return FilterInterface|null
     */
    public function createSimpleFilter($name, FilterFactoryInterface $factory)
    {
        $eqFactory = \nspl\f\partial([ $factory, 'eq' ], $name);
        $eqFilters = \nspl\a\map($eqFactory, $this->included);

        //
        // If we got some positive statements we should use only them.
        //
        if (count($eqFilters) > 0) {
            return $factory->orX($eqFilters);
        }

        $neqFactory = \nspl\f\compose(
            [ $factory, 'not' ],
            $eqFactory
        );
        $neqFilters = \nspl\a\map($neqFactory, $this->excluded);

        if (count($neqFilters) > 0) {
            return $factory->andX($neqFilters);
        }

        return null;
    }
}
