<?php

namespace CacheBundle\Feed\Fetcher;

use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Feed\Response\FeedResponseInterface;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;

/**
 * Interface FeedFetcherInterface
 *
 * Fetch document and meta information for feeds.
 *
 * @package CacheBundle\Feed\Fetcher
 */
interface FeedFetcherInterface
{

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
    public function fetch(AbstractFeed $feed, SearchRequestBuilderInterface $builder);

    /**
     * Create search builder for specified feed.
     *
     * @param AbstractFeed $feed A AbstractFeed entity instance.
     *
     * @return SearchRequestBuilderInterface|null
     */
    public function createRequestBuilder(AbstractFeed $feed);

    /**
     * Return supported feed fqcn.
     *
     * @return string
     */
    public static function support();
}
