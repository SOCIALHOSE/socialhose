<?php

namespace IndexBundle\Filter\Filters;

use IndexBundle\Filter\AbstractFilter;
use IndexBundle\Filter\FilterInterface;
use IndexBundle\Filter\Resolver\FilterResolverInterface;

/**
 * Class NotFilter
 * @package IndexBundle\Filter\Filters
 */
class NotFilter extends AbstractFilter
{

    /**
     * @var FilterInterface
     */
    protected $filter;

    /**
     * @param FilterInterface $filter A FilterInterface instance.
     */
    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Resolve given filter into proper index format.
     *
     * @param FilterResolverInterface $resolver A resolver instance used for resolving
     *                                          this filter.
     *
     * @return mixed
     */
    public function resolve(FilterResolverInterface $resolver)
    {
        return $resolver->not($this->filter);
    }

    /**
     * Get internal filter.
     *
     * @return FilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set internal filter.
     *
     * @param FilterInterface $filter A FilterInterface instance.
     *
     * @return $this
     */
    public function setFilter(FilterInterface $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * String representation of object.
     *
     * @return string the string representation of the object or null.
     */
    public function serialize()
    {
        return serialize($this->filter);
    }

    /**
     * Constructs the object.
     *
     * @param string $serialized The string representation of the object.
     *
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $filter = unserialize($serialized);

        if (! $filter instanceof FilterInterface) {
            throw new \UnexpectedValueException(sprintf(
                '%s expects that unserialized data will be instance of %s',
                static::class,
                FilterInterface::class
            ));
        }

        $this->filter = $filter;
    }

    /**
     * Sort filter values or internal filters.
     *
     * @return void
     */
    public function sort()
    {
        $this->filter->sort();
    }
}
