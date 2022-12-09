<?php

namespace CacheBundle\Feed\Fetcher;

use AppBundle\AdvancedFilters\AdvancedFiltersConfig;
use AppBundle\Manager\Source\SourceManagerInterface;
use AppBundle\Manager\StoredQuery\StoredQueryManagerInterface;
use AppBundle\Response\SearchResponse;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Entity\Feed\QueryFeed;
use CacheBundle\Feed\Response\FeedResponse;
use CacheBundle\Feed\Response\FeedResponseInterface;
use CacheBundle\Repository\StoredQueryRepository;
use Common\Enum\AFSourceEnum;
use Common\Enum\CollectionTypeEnum;
use Common\Enum\FieldNameEnum;
use Common\Enum\StoredQueryStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;

/**
 * Class QueryFeedFetcher
 *
 * Fetch document and meta information for query feed.
 *
 * @package CacheBundle\Feed\Fetcher
 */
class QueryFeedFetcher implements FeedFetcherInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var StoredQueryManagerInterface
     */
    private $manager;

    /**
     * @var SourceManagerInterface
     */
    private $sourceManager;


    /**
     * QueryFeedFetcher constructor.
     *
     * @param EntityManagerInterface      $em            A EntityManagerInterface
     *                                                   instance.
     * @param StoredQueryManagerInterface $manager       A StoredQueryManagerInterface
     *                                                   instance.
     * @param SourceManagerInterface      $sourceManager A SourceManagerInterface
     *                                                   instance.
     */
    public function __construct(
        EntityManagerInterface $em,
        StoredQueryManagerInterface $manager,
        SourceManagerInterface $sourceManager
    ) {
        $this->em = $em;
        $this->manager = $manager;
        $this->sourceManager = $sourceManager;
    }

    /**
     * Fetch information for specified feed
     *
     * @param AbstractFeed                  $feed    A AbstractFeed entity
     *                                               instance.
     * @param SearchRequestBuilderInterface $builder A SearchRequestBuilderInterface
     *                                               instance.
     *
     * @return FeedResponseInterface
     */
    public function fetch(AbstractFeed $feed, SearchRequestBuilderInterface $builder)
    {
        if (! $feed instanceof QueryFeed) {
            throw new \InvalidArgumentException(
                'Expect '. QueryFeed::class . ' but got '. get_class($feed)
            );
        }

        //
        // Get proper stored query for fetched feed.
        //
        /** @var StoredQueryRepository $repository */
        $repository = $this->em->getRepository('CacheBundle:Query\StoredQuery');
        $query = $repository->getByFeed($feed->getId());

        //
        // Collect information.
        //
        $queryStatus = $query->isInStatus([
            StoredQueryStatusEnum::INITIALIZE,
            StoredQueryStatusEnum::DELETED,
        ]);
        $response = new SearchResponse();

        $sources = $this->sourceManager->getSourcesForQuery($query, [ 'id', 'title', 'type' ]);
        $sourceLists = $this->sourceManager->getSourceListsForQuery($query, [ 'id', 'name' ]);

        $meta = [
            'type' => 'query_feed',
            'status' => $queryStatus ? 'not_synced' : 'synced',
            'search' => [
                'query' => $query->getRaw(),
                'filters' => $query->getRawFilters(),
                'advancedFilters' => count($query->getRawAdvancedFilters()) > 0 ? $query->getRawAdvancedFilters() : (object) [],
            ],
            'sources' => $sources,
            'sourceLists' => $sourceLists,
        ];

        $advancedFilters = AdvancedFiltersConfig::getDefault(AFSourceEnum::FEED);
        if (! $query->isInStatus([
            StoredQueryStatusEnum::INITIALIZE,
            StoredQueryStatusEnum::DELETED,
        ])) {
            $factory = $builder->getIndex()->getFilterFactory();
            $builder
                ->addFilter($factory->andX([
                    $factory->eq(FieldNameEnum::COLLECTION_ID, $query->getId()),
                    $factory->eq(FieldNameEnum::COLLECTION_TYPE, CollectionTypeEnum::QUERY),
                ]))
                ->addFilter($factory->not($factory->eq(FieldNameEnum::DELETE_FROM, $feed->getId())));

            $response = $this->manager->get($feed->getUser(), $query, $builder);
            $advancedFilters = $this->manager->getAdvancedFilters($query, $builder);
        }

        return new FeedResponse($response, $advancedFilters, $meta);
    }

    /**
     * Create search builder for specified feed.
     *
     * @param AbstractFeed $feed A AbstractFeed entity instance.
     *
     * @return SearchRequestBuilderInterface|null
     */
    public function createRequestBuilder(AbstractFeed $feed)
    {
        if (! $feed instanceof QueryFeed) {
            throw new \InvalidArgumentException(
                'Expect '. QueryFeed::class . ' but got '. get_class($feed)
            );
        }

        //
        // Get proper stored query for fetched feed.
        //
        /** @var StoredQueryRepository $repository */
        $repository = $this->em->getRepository('CacheBundle:Query\StoredQuery');
        $query = $repository->getByFeed($feed->getId());

        if (! $query->isInStatus([
            StoredQueryStatusEnum::INITIALIZE,
            StoredQueryStatusEnum::DELETED,
        ])) {
            return $this->manager->createRequestBuilder(
                $feed->getUser(),
                $query
            );
        }

        return null;
    }

    /**
     * Return supported feed fqcn.
     *
     * @return string
     */
    public static function support()
    {
        return QueryFeed::class;
    }
}
