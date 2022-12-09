<?php

namespace AppBundle\Response;

use CacheBundle\Entity\Query\SimpleQuery;

/**
 * Class SearchAndCacheResponse
 *
 * Response from searchAndCache method of SimpleQueryManager.
 *
 * @package AppBundle\Response
 */
class SearchAndCacheResponse extends SearchResponse
{

    /**
     * @var SimpleQuery
     */
    private $query;

    /**
     * @param SimpleQuery $query              A SimpleQuery entity.
     * @param array       $documents          Array of results.
     * @param array       $aggregationResults Array of results of aggregation.
     * @param integer     $totalCount         Total available counts.
     * @param integer     $uniqueCount        Count of unique documents added
     *                                        to cache.
     */
    public function __construct(
        SimpleQuery $query,
        array $documents = [],
        array $aggregationResults = [],
        $totalCount = 0,
        $uniqueCount = 0
    ) {
        parent::__construct($documents, $aggregationResults, $totalCount, $uniqueCount, true);
        $this->query = $query;
    }

    /**
     * @param SimpleQuery    $query    A SimpleQuery entity.
     * @param SearchResponse $response A SearchResponse instance.
     *
     * @return SearchAndCacheResponse
     */
    public static function fromSearchResponse(SimpleQuery $query, SearchResponse $response)
    {
        return new self(
            $query,
            $response->getDocuments(),
            $response->getAggregationResults(),
            $response->getTotalCount(),
            $response->getUniqueCount()
        );
    }

    /**
     * @return SimpleQuery
     */
    public function getQuery()
    {
        return $this->query;
    }
}
