<?php

namespace AppBundle\Response;

use IndexBundle\Model\DocumentInterface;

/**
 * Interface SearchResponseInterface
 *
 * Main interface for each response in application. This interface
 * using in paginator.
 *
 * @package AppBundle\Response
 */
interface SearchResponseInterface extends \Countable, \ArrayAccess, \IteratorAggregate
{

    /**
     * Get documents from response.
     *
     * @return DocumentInterface[]
     */
    public function getDocuments();

    /**
     * @param callable|\Closure $callback Mapper callback.
     *
     * @return SearchResponse
     */
    public function mapDocuments($callback);

    /**
     * Get response aggregation results.
     *
     * @return array
     */
    public function getAggregationResults();

    /**
     * Get total count of available results.
     *
     * @return integer
     */
    public function getTotalCount();

    /**
     * Get unique documents count.
     *
     * @return integer
     */
    public function getUniqueCount();

    /**
     * @return boolean
     */
    public function isFromCache();
}
