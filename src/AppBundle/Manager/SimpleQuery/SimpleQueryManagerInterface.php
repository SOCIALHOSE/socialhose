<?php

namespace AppBundle\Manager\SimpleQuery;

use AppBundle\Response\SearchAndCacheResponse;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Interface SimpleQueryManagerInterface
 *
 * @package AppBundle\Manager\StoredQuery
 */
interface SimpleQueryManagerInterface
{

    /**
     * @param SearchRequestInterface $request            A SearchRequestInterface
     *                                                   instance.
     * @param array                  $rawFilters         A filters as is.
     * @param array                  $rawAdvancedFilters A advanced filters as is.
     *
     * @return SearchAndCacheResponse
     *
     * todo remove $rawFilters and $rawAdvancedFilters 'cause it should be computed.
     */
    public function searchAndCache(
        SearchRequestInterface $request,
        array $rawFilters,
        array $rawAdvancedFilters
    );
}
