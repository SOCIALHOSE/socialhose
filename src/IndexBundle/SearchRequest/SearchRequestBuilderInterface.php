<?php

namespace IndexBundle\SearchRequest;

use CacheBundle\Entity\Query\AbstractQuery;
use IndexBundle\Aggregation\AggregationInterface;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use IndexBundle\Filter\FilterInterface;
use IndexBundle\Index\IndexInterface;
use UserBundle\Entity\User;

/**
 * Interface SearchRequestBuilderInterface
 * @package IndexBundle\SearchRequest
 */
interface SearchRequestBuilderInterface extends ImmutableSearchRequestInterface
{

    /**
     * @return IndexInterface
     */
    public function getIndex();

    /**
     * Get filter factory for this search request builder.
     *
     * @return FilterFactoryInterface
     */
    public function getFilterFactory();

    /**
     * Set filters, override already exists.
     *
     * @param FilterInterface|FilterInterface[] $filters A FilterInterface
     *                                                   instance or array of
     *                                                   FilterInterface's
     *                                                   instances.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setFilters($filters);

    /**
     * Add new filter.
     *
     * @param FilterInterface $filter A FilterInterface instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function addFilter(FilterInterface $filter);

    /**
     * Set aggregation
     *
     * @param AggregationInterface|AggregationInterface[] $aggregation A
     *                                                                 AggregationInterface
     *                                                                 instance
     *                                                                 or array
     *                                                                 of instances.
     *
     * @return static
     */
    public function setAggregation($aggregation);

    /**
     * Set user.
     *
     * @param User $user A user who made search request.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setUser(User $user = null);

    /**
     * Set raw search query.
     *
     * @param string $query Raw search query.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setQuery($query);

    /**
     * Set fields, override already exists.
     *
     * @param array<string> $fields Fields names.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setFields(array $fields);

    /**
     * Set fetched source fields names.
     *
     * @param string[]|array $sources Array of fetched source fields. Fetch all if
     *                                empty.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setSources(array $sources);

    /**
     * Add new field name.
     *
     * @param string $field Field name.
     *
     * @return SearchRequestBuilderInterface
     */
    public function addField($field);

    /**
     * Add sorting by specified field.
     *
     * @param string $fieldName Field name.
     * @param string $direction Sorting direction, must be 'asc' or 'desc'.
     *
     * @return SearchRequestBuilderInterface
     */
    public function addSort($fieldName, $direction = 'asc');

    /**
     * Set new sorting fields.
     *
     * @param array $sortFields Assoc array where key is field name and value is
     *                          sorting direction.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setSorts(array $sortFields);

    /**
     * Build search request.
     *
     * @return SearchRequestInterface
     */
    public function build();

    /**
     * Initialize builder parameters from specified search request.
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function fromSearchRequest(SearchRequestInterface $request);

    /**
     * Initialize builder parameters from specified search request.
     *
     * @param SearchRequestBuilderInterface $builder A SearchRequestBuilderInterface instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function fromSearchRequestBuilder(SearchRequestBuilderInterface $builder);

    /**
     * Initialize builder parameters from specified query entity.
     *
     * @param AbstractQuery $query A AbstractQuery entity instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function fromQueryEntity(AbstractQuery $query);
}
