<?php

namespace IndexBundle\Filter\Resolver;

use Common\Enum\PublisherTypeEnum;
use IndexBundle\Filter\FilterInterface;
use IndexBundle\Index\Strategy\IndexStrategyInterface;

/**
 * Class ElasticSearchFilterResolver
 * Implementation of FilterResolverInterface for Elasticsearch index.
 *
 * @package IndexBundle\Filter\Resolver
 *
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
class ElasticSearchFilterResolver implements FilterResolverInterface
{

    /**
     * @var IndexStrategyInterface
     */
    private $indexStrategy;

    /**
     * ElasticSearchFilterResolver constructor.
     *
     * @param IndexStrategyInterface $indexStrategy A IndexStrategyInterface
     *                                              instance.
     */
    public function __construct(IndexStrategyInterface $indexStrategy)
    {
        $this->indexStrategy = $indexStrategy;
    }

    /**
     * Generate 'greater or equal to' filter for search request.
     *
     * @param string $field Filtered filed name.
     * @param string $value Filter value.
     *
     * @return mixed
     */
    public function gte($field, $value)
    {
        $field = $this->indexStrategy->denormalizeFieldName($field);
        $value = $this->denormalizeValue($value);

        return $field .':['. $value .' TO *]';
    }

    /**
     * Generate 'greater then' filter for search request.
     *
     * @param string $field Filtered filed name.
     * @param string $value Filter value.
     *
     * @return mixed
     */
    public function gt($field, $value)
    {
        $field = $this->indexStrategy->denormalizeFieldName($field);
        $value = $this->denormalizeValue($value);

        return $field .':{'. $value .' TO *]';
    }

    /**
     * Generate 'equal to' filter for search request.
     *
     * @param string $field Filtered filed name.
     * @param string $value Filter value.
     *
     * @return mixed
     */
    public function eq($field, $value)
    {
        $field = $this->indexStrategy->denormalizeFieldName($field);
        $value = $this->denormalizeValue($value);

        return $field .':'. $value;
    }

    /**
     * Generate 'less then' filter for search request.
     *
     * @param string $field Filtered filed name.
     * @param string $value Filter value.
     *
     * @return mixed
     */
    public function lt($field, $value)
    {
        $field = $this->indexStrategy->denormalizeFieldName($field);
        $value = $this->denormalizeValue($value);

        return $field .':[* TO '. $value .'}';
    }

    /**
     * Generate 'less or equal to' filter for search request.
     *
     * @param string $field Filtered filed name.
     * @param string $value Filter value.
     *
     * @return mixed
     */
    public function lte($field, $value)
    {
        $field = $this->indexStrategy->denormalizeFieldName($field);
        $value = $this->denormalizeValue($value);

        return $field .':[* TO '. $value .']';
    }

    /**
     * Generate 'in' filter for search request.
     *
     * @param string $field  Filtered filed name.
     * @param array  $values Filter values.
     *
     * @return mixed
     */
    public function in($field, array $values)
    {
        $field = $this->indexStrategy->denormalizeFieldName($field);
        return $field .':('. implode(' OR ', $this->denormalizeValue($values)) .')';
    }

    /**
     * Generate regexp filter for search request.
     *
     * @param string $field   Filtered field name.
     * @param string $pattern Regexp pattern.
     *
     * @return mixed
     */
    public function regex($field, $pattern)
    {
        $field = $this->indexStrategy->denormalizeFieldName($field);

        return $field .':/'. $pattern .'/';
    }

    /**
     * Generate 'not' filter for search request.
     *
     * @param FilterInterface $filter A FilterInterface instance.
     *
     * @return mixed
     */
    public function not(FilterInterface $filter)
    {
        return 'NOT ('. $filter->resolve($this) .')';
    }

    /**
     * Generate 'and' filter for search request.
     *
     * @param FilterInterface|FilterInterface[] $filters A FilterInterface
     *                                                   instance or array of
     *                                                   instances.
     *
     * @return mixed
     */
    public function andX($filters)
    {
        return implode(' AND ', array_map(function (FilterInterface $filter) {
            return '('. $filter->resolve($this) .')';
        }, $filters));
    }

    /**
     * Generate 'or' filter for search request.
     *
     * @param FilterInterface|FilterInterface[] $filters A FilterInterface
     *                                                   instance or array of
     *                                                   instances.
     *
     * @return mixed
     */
    public function orX($filters)
    {
        return implode(' OR ', array_map(function (FilterInterface $filter) {
            return '('. $filter->resolve($this) .')';
        }, $filters));
    }

    /**
     * Denormalize filter value.
     *
     * @param mixed $value Filter value.
     *
     * @return mixed
     */
    private function denormalizeValue($value)
    {
        switch (true) {
            case PublisherTypeEnum::isValid($value):
                //
                // Denormalize publisher types.
                //
                $value = implode(' OR ', $this->indexStrategy->denormalizePublisherType($value));
                break;

            // todo add proper code for denormalization of this values
            // case LanguageEnum::isValid($value):
            // case CountryEnum::isValid($value):
            // case StateEnum::isValid($value):

            //
            // We should quote all text only if it's not regexp and not already
            // wrapped by quote's or bracket's.
            //
            case is_string($value)
                && ! in_array($value[0], [ '/', '"', '(' ], true)
                && ! in_array($value[strlen($value) - 1], [ '/', '"', ')' ], true):
                $value = '"'. $value .'"';
                break;

            //
            // Denormalize date.
            //
            case $value instanceof \DateTimeInterface:
                $value = $value->format('c');
                break;

            //
            // Denormalize all values in array.
            //
            case is_array($value):
                $value = array_map([ $this, 'denormalizeValue' ], $value);
                break;
        }

        return $value;
    }
}
