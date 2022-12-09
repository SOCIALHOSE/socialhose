<?php

namespace IndexBundle\Index;

use AppBundle\AdvancedFilters\AFResolverInterface;
use AppBundle\Response\SearchResponseInterface;
use IndexBundle\Aggregation\AggregationFacadeInterface;
use IndexBundle\Aggregation\Factory\AggregationFactoryInterface;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Interface IndexInterface
 *
 * Common index interface.
 *
 * @package IndexBundle\Index
 */
interface IndexInterface
{

    /**
     * Max result fetched from documents.
     *
     * Affects on pagination too so requests can't fetch one document from 101
     * page if this parameters is set to 100.
     */
    const MAX_RESULT_COUNT = 5000;

    /**
     * Search information in index.
     *
     * @param SearchRequestInterface $request Internal representation of search
     *                                        request.
     *
     * @return SearchResponseInterface
     */
    public function search(SearchRequestInterface $request);

    /**
     * Fetch all relevant documents.
     *
     * @param SearchRequestInterface $request Internal representation of search
     *                                        request.
     *
     * @return \Traversable
     */
    public function fetchAll(SearchRequestInterface $request);

    /**
     * Get documents by it ids.
     *
     * @param integer|integer[] $ids    Array of document ids or single id.
     * @param string|string[]   $fields Array of requested fields of single field.
     *
     * @return DocumentInterface[]
     */
    public function get($ids, $fields = []);

    /**
     * Check that specified documents is exists.
     *
     * @param integer|array $ids Array of document ids or single id.
     *
     * @return array Contains all ids which not found in index.
     */
    public function has($ids);

    /**
     * Create request builder instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function createRequestBuilder();

    /**
     * Get strategy used by this index.
     *
     * @return IndexStrategyInterface
     */
    public function getStrategy();

    /**
     * Get filter resolver.
     *
     * @return FilterFactoryInterface
     */
    public function getFilterFactory();

    /**
     * Get aggregation filter resolver.
     *
     * @return AFResolverInterface
     */
    public function getAFResolver();

    /**
     * Get aggregation factory instance
     *
     * @return AggregationFactoryInterface
     */
    public function getAggregationFactory();

    /**
     * Get aggregation instance
     *
     * @return AggregationFacadeInterface
     */
    public function getAggregation();

    /**
     * Get Total
     *
     * @param SearchRequestInterface $request Internal representation of search
     *                                        request.
     *
     * @return integer
     */
    public function getTotal(SearchRequestInterface $request);
}
