<?php

namespace AppBundle\Manager\StoredQuery;

use AppBundle\Manager\AbstractQueryManager;
use AppBundle\Response\SearchResponseInterface;
use CacheBundle\Entity\DocumentCollectionInterface;
use CacheBundle\Entity\Query\StoredQuery;
use CacheBundle\Repository\StoredQueryRepository;
use Common\Enum\CollectionTypeEnum;
use Common\Enum\FieldNameEnum;
use Common\Enum\StoredQueryStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Index\External\ExternalIndexInterface;
use IndexBundle\Index\Internal\InternalIndexInterface;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use UserBundle\Entity\User;

/**
 * Class StoredQueryManager
 * Default implementation of StoredQueryManagerInterface.
 *
 * @package AppBundle\Manager\StoredQuery
 */
class StoredQueryManager extends AbstractQueryManager implements
    StoredQueryManagerInterface
{

    /**
     * @var ExternalIndexInterface
     */
    private $externalIndex;

    /**
     * @var InternalIndexInterface
     */
    private $internalIndex;

    /**
     * @var integer
     */
    private $pageSize;

    /**
     * @param ExternalIndexInterface $externalIndex A ExternalIndexInterface instance.
     * @param InternalIndexInterface $internalIndex A InternalIndexInterface instance.
     * @param EntityManagerInterface $em            A EntityManagerInterface instance.
     * @param integer                $pageSize      Size of page.
     */
    public function __construct(
        ExternalIndexInterface $externalIndex,
        InternalIndexInterface $internalIndex,
        EntityManagerInterface $em,
        $pageSize
    ) {
        parent::__construct($em);
        $this->externalIndex = $externalIndex;
        $this->internalIndex = $internalIndex;
        $this->pageSize = $pageSize;
    }

    /**
     * Fetch documents for specified stored query.
     *
     * @param StoredQuery $query A StoredQuery entity instance for which we
     *                           should fetch documents.
     *
     * @return StoredQuery
     */
    public function fetchDocuments(StoredQuery $query)
    {
        //
        // Set total count to 0 because when we cache founded results they counts
        // automatically will be sum with query total count.
        //
        $query->setTotalCount(0);

        //
        // Make first request in order to fetch real total count.
        //
        $request = $this->externalIndex
            ->createRequestBuilder()
            ->setQuery($query->getRaw())
            ->addSort(FieldNameEnum::PUBLISHED, 'desc')
            ->setLimit($this->pageSize)
            ->setFilters($query->getFilters())
            ->setFields([
                FieldNameEnum::TITLE,
                FieldNameEnum::MAIN,
            ]);

        if ($query->isInStatus(StoredQueryStatusEnum::SYNCED)) {
            $factory = $this->externalIndex->getFilterFactory();
            $request->addFilter($factory->gte(FieldNameEnum::PUBLISHED, $query->getLastUpdateAt()->format('c')));
        }

        $request = $request->build();

        foreach ($this->externalIndex->fetchAll($request) as $response) {
            $this->cache($response, $query, $request->getIndex()->getStrategy(), 1);

            //
            // Remove all previously fetched values.
            //
            // Need in order to insure that memory will be free before fetch new
            // part of data.
            //
            unset($response);
            gc_collect_cycles();
        }

        return $query->setStatus(StoredQueryStatusEnum::SYNCED);
    }

    /**
     * Create new stored query.
     *
     * @param SearchRequestBuilderInterface $builder            A SearchRequestBuilderInterface
     *                                                          instance.
     * @param array                         $rawFilters         A raw filters.
     * @param array                         $rawAdvancedFilters A raw advanced filters.
     *
     * @return StoredQuery
     */
    public function createQuery(
        SearchRequestBuilderInterface $builder,
        array $rawFilters,
        array $rawAdvancedFilters
    ) {
        $externalBuilder = $this->externalIndex->createRequestBuilder()
            ->fromSearchRequestBuilder($builder);

        // Set necessary request builder parameters.
        $searchRequest = $builder
            ->setFields([
                FieldNameEnum::TITLE,
                FieldNameEnum::MAIN,
            ])
            ->setPage(1)
            ->setLimit($this->pageSize)
            ->build();

        $externalSearchRequest = $externalBuilder
            ->setFields([
                FieldNameEnum::TITLE,
                FieldNameEnum::MAIN,
            ])
            ->setPage(1)
            ->setLimit($this->pageSize)
            ->build();

        // Try to find stored query with same hash.
        /** @var StoredQueryRepository $repository */
        $repository = $this->em->getRepository(StoredQuery::class);
        $query = $repository->get($searchRequest->getHash());

        if ($query === null) {
            //
            // We have unique stored search query.
            // Make request in order to get and check total document count.
            //
            $response = $externalSearchRequest->execute();

            //
            // Fill internal query part.
            //
            /** @var StoredQuery $query */
            $query = StoredQuery::create()
                ->setRaw($builder->getQuery())
                ->setFields($builder->getFields())
                ->setNormalized($searchRequest->getNormalizedQuery())
                ->setFilters($searchRequest->getFilters())
                ->setRawFilters($rawFilters)
                ->setRawAdvancedFilters($rawAdvancedFilters)
                ->setTotalCount($response->getTotalCount())
                ->setHash($searchRequest->getHash());

            // TODO maybe we should store first page right here and fetch documents from second page?
            $this->em->persist($query);
        }

        return $query;
    }

    /**
     * @param SearchRequestBuilderInterface $builder
     * @param array                         $rawFilters
     * @param array                         $rawAdvancedFilters
     * @return integer
     */
    public function getTotal(
        SearchRequestBuilderInterface $builder,
        array $rawFilters,
        array $rawAdvancedFilters
    ) {
        $externalBuilder = $this->externalIndex->createRequestBuilder()
            ->fromSearchRequestBuilder($builder);

        $externalSearchRequest = $externalBuilder
            ->setFields([
                FieldNameEnum::TITLE,
                FieldNameEnum::MAIN,
            ])
            ->build();
        return $externalSearchRequest->getIndex()->getTotal($externalSearchRequest);
    }


    /**
     * Get documents from cache.
     *
     * @param User                          $user    User who requested documents.
     * @param StoredQuery                   $query   A StoredQuery entity instance.
     * @param SearchRequestBuilderInterface $builder A SearchRequestBuilderInterface
     *                                               instance.
     *
     * @return SearchResponseInterface
     */
    public function get(
        User $user,
        StoredQuery $query,
        SearchRequestBuilderInterface $builder
    ) {
        $factory = $this->internalIndex->getFilterFactory();
        $filters = $query->getFilters();
        $filters = array_merge($filters, $builder->getFilters());

        $request = $this->internalIndex->createRequestBuilder()
            ->setUser($user)
            ->setQuery($query->getRaw())
            ->setFields([
                FieldNameEnum::TITLE,
                FieldNameEnum::MAIN,
            ])
            ->setFilters($filters)
            ->addFilter($factory->eq(FieldNameEnum::COLLECTION_ID, $query->getId()))
            ->addFilter($factory->eq(FieldNameEnum::COLLECTION_TYPE, CollectionTypeEnum::QUERY))
            ->setPage($builder->getPage())
            ->setLimit($this->pageSize)
            ->addSort(FieldNameEnum::PUBLISHED, 'desc')
            ->build();

        return $request->execute();
    }

    /**
     * Get documents from cache.
     *
     * Get all matched document but from specified date.
     *
     * @param User        $user  User who requested documents.
     * @param StoredQuery $query A StoredQuery entity instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function createRequestBuilder(User $user, StoredQuery $query)
    {
        $factory = $this->internalIndex->getFilterFactory();
        $filters = $query->getFilters();
        $filters[] = $factory->eq(FieldNameEnum::COLLECTION_ID, $query->getId());
        $filters[] = $factory->eq(FieldNameEnum::COLLECTION_TYPE, CollectionTypeEnum::QUERY);

        return $this->internalIndex->createRequestBuilder()
            ->setUser($user)
            ->setQuery($query->getRaw())
            ->setFields([
                FieldNameEnum::TITLE,
                FieldNameEnum::MAIN,
            ])
            ->setFilters($filters);
    }

    /**
     * Get advanced filters for specified query.
     *
     * @param StoredQuery                   $query   A StoredQuery entity instance.
     * @param SearchRequestBuilderInterface $builder A SearchRequestBuilderInterface
     *                                               instance.
     *
     * @return array
     */
    public function getAdvancedFilters(
        StoredQuery $query,
        SearchRequestBuilderInterface $builder
    ) {
        $searchBuilder = $this->internalIndex->createRequestBuilder()
            ->fromQueryEntity($query);

        $searchBuilder
            ->setFilters(array_merge($searchBuilder->getFilters(), $builder->getFilters()));

        return $searchBuilder->build()->getAvailableAdvancedFilters();
    }

    /**
     * @param SearchResponseInterface     $response   A SearchResponseInterface
     *                                                instance..
     * @param DocumentCollectionInterface $collection A DocumentCollectionInterface
     *                                                entity instance.
     * @param IndexStrategyInterface      $strategy   A DocumentNormalizerInterface
     *                                                instance.
     * @param integer                     $pageNumber A requested page number.
     *
     * @return SearchResponseInterface
     */
    protected function cache(
        SearchResponseInterface $response,
        DocumentCollectionInterface $collection,
        IndexStrategyInterface $strategy,
        $pageNumber
    ) {
        $response = parent::cache($response, $collection, $strategy, $pageNumber);

        $response->mapDocuments(function (DocumentInterface $document) use ($collection) {
            return $document->mapRawData(function (array $data) use ($collection) {
                $data[FieldNameEnum::COLLECTION_ID] = $collection->getCollectionId();
                $data[FieldNameEnum::COLLECTION_TYPE] = $collection->getCollectionType()->getValue();

                return $data;
            });
        });

        $this->internalIndex->index($response->getDocuments());
        $collection->setTotalCount($collection->getTotalCount() + count($response));

        return $response;
    }
}
