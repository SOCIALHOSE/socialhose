<?php

namespace CacheBundle\Feed\Fetcher;

use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Entity\Feed\ClipFeed;
use CacheBundle\Feed\Response\FeedResponse;
use CacheBundle\Feed\Response\FeedResponseInterface;
use Common\Enum\CollectionTypeEnum;
use Common\Enum\FieldNameEnum;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Index\Internal\InternalIndexInterface;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;

/**
 * Class ClipFeedFetcher
 *
 * Fetch document and meta information for query feed.
 *
 * @package CacheBundle\Feed\Fetcher
 */
class ClipFeedFetcher implements FeedFetcherInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var InternalIndexInterface
     */
    private $index;

    /**
     * QueryFeedFetcher constructor.
     *
     * @param EntityManagerInterface $em    A EntityManagerInterface instance.
     * @param InternalIndexInterface $index A InternalIndexInterface instance.
     */
    public function __construct(
        EntityManagerInterface $em,
        InternalIndexInterface $index
    ) {
        $this->em = $em;
        $this->index = $index;
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
        if (! $feed instanceof ClipFeed) {
            throw new \InvalidArgumentException(
                'Expect '. ClipFeed::class . ' but got '. get_class($feed)
            );
        }

        $factory = $this->index->getFilterFactory();
        $request = $this->index->createRequestBuilder()
            ->setFilters($builder->getFilters())
            ->addFilter($factory->eq(FieldNameEnum::COLLECTION_ID, $feed->getId()))
            ->addFilter($factory->eq(FieldNameEnum::COLLECTION_TYPE, CollectionTypeEnum::FEED))
            ->build();

        return new FeedResponse(
            $request->execute(),
            $request->getAvailableAdvancedFilters(), // AFSourceEnum::FEED
            [
                'type' => 'clip_feed',
                'status' => 'synced',
                'search' => [
                    'advancedFilters' => count($feed->getRawFilters()) > 0 ? $feed->getRawFilters() : (object) [],
                ],
            ]
        );
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
        if (! $feed instanceof ClipFeed) {
            throw new \InvalidArgumentException(
                'Expect '. ClipFeed::class . ' but got '. get_class($feed)
            );
        }

        $factory = $this->index->getFilterFactory();
        $filters = $feed->getFilters();
        $filters[] = $factory->eq(FieldNameEnum::COLLECTION_ID, $feed->getId());
        $filters[] = $factory->eq(FieldNameEnum::COLLECTION_TYPE, CollectionTypeEnum::FEED);

        return $this->index->createRequestBuilder()
            ->setFilters($filters)
            ->setFields([
                FieldNameEnum::TITLE,
                FieldNameEnum::MAIN,
            ]);
    }

    /**
     * Return supported feed fqcn.
     *
     * @return string
     */
    public static function support()
    {
        return ClipFeed::class;
    }
}
