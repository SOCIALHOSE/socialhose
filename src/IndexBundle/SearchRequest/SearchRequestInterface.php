<?php

namespace IndexBundle\SearchRequest;

use AppBundle\Response\SearchResponseInterface;
use IndexBundle\Index\IndexInterface;

/**
 * Interface SearchRequestInterface
 * Internal representation of search request.
 *
 * @package IndexBundle\SearchRequest
 */
interface SearchRequestInterface extends ImmutableSearchRequestInterface
{

    /**
     * @return IndexInterface
     */
    public function getIndex();

    /**
     * Get normalized query.
     *
     * @return string
     */
    public function getNormalizedQuery();

    /**
     * Compute this response hash.
     *
     * @return string
     */
    public function getHash();

    /**
     * Execute this search request and get response from server.
     *
     * @return SearchResponseInterface
     */
    public function execute();

    /**
     * Get available advanced filters for this request.
     *
     * @return array
     */
    public function getAvailableAdvancedFilters();
}
