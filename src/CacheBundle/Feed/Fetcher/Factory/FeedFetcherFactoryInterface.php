<?php

namespace CacheBundle\Feed\Fetcher\Factory;

use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Feed\Fetcher\FeedFetcherInterface;

/**
 * Interface FeedFetcherFactoryInterface
 *
 * Return feed fetcher factory.
 *
 * @package CacheBundle\Feed\Fetcher\Factory
 */
interface FeedFetcherFactoryInterface
{

    /**
     * Get feed fetcher for specified feed.
     *
     * @param string|AbstractFeed $feedClass Feed fqcn or instance.
     *
     * @return FeedFetcherInterface
     */
    public function get($feedClass);
}
