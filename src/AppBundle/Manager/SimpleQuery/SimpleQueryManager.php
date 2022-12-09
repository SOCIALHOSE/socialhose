<?php

namespace AppBundle\Manager\SimpleQuery;

use AppBundle\Manager\AbstractQueryManager;
use AppBundle\Response\SearchAndCacheResponse;
use AppBundle\Response\SearchResponse;
use CacheBundle\Entity\Document;
use CacheBundle\Entity\Query\SimpleQuery;
use CacheBundle\Repository\DocumentRepository;
use CacheBundle\Repository\SimpleQueryRepository;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Class SimpleQueryManager
 *
 * @package AppBundle\Manager\StoredQuery
 */
class SimpleQueryManager extends AbstractQueryManager implements
    SimpleQueryManagerInterface
{

    /**
     * @var integer
     */
    private $lifetime;

    /**
     * AbstractQueryManager constructor.
     *
     * @param EntityManagerInterface $em       A EntityManagerInterface instance.
     * @param integer                $lifetime SimpleQuery lifetime.
     */
    public function __construct(EntityManagerInterface $em, $lifetime)
    {
        parent::__construct($em);
        $this->lifetime = $lifetime;
    }

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
    ) {
        /** @var SimpleQueryRepository $queryRepository */
        $queryRepository = $this->em->getRepository(SimpleQuery::class);

        //
        // Try to get simple query for given request in our cache.
        //
        $query = $queryRepository->get($request->getHash());
        $response = null;
        if ($query instanceof SimpleQuery) {
            if ($query->getTotalCount() === 0) {
                $response = new SearchResponse();
            } else {
                //
                // Get documents from cache.
                //
                /** @var DocumentRepository $repository */
                $repository = $this->em->getRepository(Document::class);

                $response = new SearchResponse(
                    $this->articleDocumentsFromEntities(
                        $repository->getForQuery($query->getId(), $request->getPage()),
                        $request->getIndex()->getStrategy()
                    ),
                    [],
                    $query->getTotalCount()
                );
                if (count($response) === 0) {
                    //
                    // We don't have documents for requested query, so we
                    // should make request to index.
                    //
                    $response = null;
                }
            }
        }

        //
        // We need to make request to index because no one of users did not make
        // similar request or specified page don't requested yet.
        //
        if ($response === null) {
            $response = $request->execute();

            if (! $query instanceof SimpleQuery) {
                //
                // Create new query entity instance for current search request.
                //
                $query = SimpleQuery::fromSearchRequest($request)
                    ->setTotalCount($response->getTotalCount())
                    ->setExpirationDate($this->lifetime)
                    ->setRawFilters($rawFilters) // todo should be computed from request
                    ->setRawAdvancedFilters($rawAdvancedFilters); // todo should be computed from request
            }

            $cacheResponse = $this->cache(
                $response,
                $query,
                $request->getIndex()->getStrategy(),
                $request->getPage()
            );
            $this->em->persist($query);
            $this->em->flush();

            $response = new SearchResponse(
                $cacheResponse->getDocuments(),
                [],
                $response->getTotalCount(),
                $cacheResponse->getUniqueCount()
            );
        }

        return SearchAndCacheResponse::fromSearchResponse($query, $response);
    }
}
