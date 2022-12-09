<?php

namespace IndexBundle\Filter;

use IndexBundle\Filter\Filters\AndFilter;
use IndexBundle\Filter\Filters\InFilter;
use IndexBundle\Filter\Filters\NotFilter;
use IndexBundle\Filter\Filters\OrFilter;

/**
 * Class AbstractGroupFilter
 * Base class for all group filters.
 *
 * @package IndexBundle\Filter
 */
abstract class AbstractGroupFilter extends AbstractFilter implements
    GroupFilterInterface
{

    private static $priorityTable = [
        Filters\EqFilter::class => 0,
        Filters\GteFilter::class => 0,
        Filters\GtFilter::class => 0,
        Filters\LteFilter::class => 0,
        Filters\LtFilter::class => 0,
        Filters\InFilter::class => 1,
        Filters\NotFilter::class => 2,
        Filters\AndFilter::class => 3,
        Filters\OrFilter::class => 4,
    ];

    /**
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * @param FilterInterface|FilterInterface[] $filters FilterInterface
     *                                                   instance or array of
     *                                                   instances.
     */
    public function __construct($filters = [])
    {
        if (! is_array($filters)) {
            $filters = [ $filters ];
        }

        $filters = array_filter($filters);

        if (! \nspl\a\all($filters, \nspl\f\rpartial(\app\op\isInstanceOf, FilterInterface::class))) {
            throw new \InvalidArgumentException('\'$filters\' should be array of FilterInterface instances or single instance');
        }

        $this->filters = $filters;
    }

    /**
     * Add new filter to group.
     *
     * @param FilterInterface $filter A FilterInterface instance.
     *
     * @return GroupFilterInterface
     */
    public function add(FilterInterface $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Get all internal filters.
     *
     * @return FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set internal filters.
     *
     * @param FilterInterface[]|array $filters Array of FilterInterface instances.
     *
     * @return static
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Count elements of an object
     *
     * @return integer
     */
    public function count()
    {
        return count($this->filters);
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->filters);
    }

    /**
     * @param string $serialized The string representation of the object.
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        $filters = unserialize($serialized);

        if (! \nspl\a\all($filters, \nspl\f\rpartial(\app\op\isInstanceOf, FilterInterface::class))) {
            throw new \UnexpectedValueException(sprintf(
                '%s expects that unserialized data will contains array of %s instances',
                static::class,
                FilterInterface::class
            ));
        }

        $this->filters = $filters;
    }

    /**
     * Sort filter values or internal filters.
     *
     * @return void
     */
    public function sort()
    {
        \nspl\a\map(\nspl\op\methodCaller('sort'), $this->filters);

        usort($this->filters, [ $this, 'compareFilters' ]);
    }

    /**
     * @param FilterInterface $lfilter Left compared filter.
     * @param FilterInterface $rfilter Right compared filter.
     *
     * @return integer
     */
    private function compareFilters(FilterInterface $lfilter, FilterInterface $rfilter)
    {
        $lpriority = self::$priorityTable[get_class($lfilter)];
        $rpriority = self::$priorityTable[get_class($rfilter)];

        if ($lpriority === $rpriority) {
            return $this->compareEqualFilters($lfilter, $rfilter);
        }

        return $lpriority < $rpriority ? -1 : 1;
    }

    /**
     * @param FilterInterface $lfilter Left compared filter.
     * @param FilterInterface $rfilter Right compared filter.
     *
     * @return integer
     */
    private function compareEqualFilters(FilterInterface $lfilter, FilterInterface $rfilter)
    {
        switch (true) {
            case ($lfilter instanceof AbstractValueFilter) && ($rfilter instanceof AbstractValueFilter):
                return $this->compareValueFilters($lfilter, $rfilter);

            case ($lfilter instanceof InFilter) && ($rfilter instanceof InFilter):
                return $this->compareInFilters($lfilter, $rfilter);

            case ($lfilter instanceof NotFilter) && ($rfilter instanceof NotFilter):
                return $this->compareFilters($lfilter->getFilter(), $rfilter->getFilter());

            case (($lfilter instanceof AndFilter) && ($rfilter instanceof AndFilter)):
            case ($lfilter instanceof OrFilter) && ($rfilter instanceof OrFilter):
                return $this->compareGroupFilters($lfilter, $rfilter);
        }

        throw new \LogicException('Unhandled filters comparing situations.');
    }

    /**
     * @param AbstractValueFilter $lfilter Left compared filter.
     * @param AbstractValueFilter $rfilter Right compared filter.
     *
     * @return integer
     */
    private function compareValueFilters(
        AbstractValueFilter $lfilter,
        AbstractValueFilter $rfilter
    ) {
        $cmpRes = strcmp(
            strtolower($lfilter->getFieldName()),
            strtolower($rfilter->getFieldName())
        );

        if ($cmpRes === 0) {
            $lvalue = $lfilter->getValue();
            $rvalue = $rfilter->getValue();

            if ($lvalue !== $rvalue) {
                $cmpRes = $lvalue < $rvalue ? -1 : 1;
            }
        }

        return $cmpRes;
    }

    /**
     * @param InFilter $lfilter Left compared filter.
     * @param InFilter $rfilter Right compared filter.
     *
     * @return integer
     */
    private function compareInFilters(InFilter $lfilter, InFilter $rfilter)
    {
        $cmpRes = strcmp(
            strtolower($lfilter->getFieldName()),
            strtolower($rfilter->getFieldName())
        );

        if ($cmpRes === 0) {
            $lvalue = $lfilter->getValue();
            $rvalue = $rfilter->getValue();

            if ($lvalue !== $rvalue) {
                $cmpRes = $lvalue < $rvalue ? -1 : 1;
            }
        }

        return $cmpRes;
    }

    /**
     * @param AbstractGroupFilter $lfilter Left compared filter.
     * @param AbstractGroupFilter $rfilter Right compared filter.
     *
     * @return integer
     */
    private function compareGroupFilters(
        AbstractGroupFilter $lfilter,
        AbstractGroupFilter $rfilter
    ) {
        $lfilters = $lfilter->getFilters();
        $rfilters = $rfilter->getFilters();
        $lfiltersCount = count($lfilters);
        $rfiltersCount = count($rfilters);

        if ($lfiltersCount === $rfiltersCount) {
            for ($i = 0; $i < $lfiltersCount; ++$i) {
                $res = $this->compareFilters($lfilters[$i], $rfilters[$i]);

                if ($res !== 0) {
                    return $res;
                }
            }

            return 0;
        }

        return $lfiltersCount < $rfiltersCount ? -1 : 1;
    }
}
