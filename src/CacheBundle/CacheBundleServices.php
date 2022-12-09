<?php

namespace CacheBundle;

/**
 * Class CacheBundleServices
 * @package CacheBundle
 */
abstract class CacheBundleServices
{

    /**
     * Cache storage.
     *
     * Implements {@see \CacheBundle\Cache\CacheInterface} interface.
     */
    const CACHE = 'app.feed_manager';

    /**
     * Source cache storage.
     *
     * Implements {@see \CacheBundle\Cache\SourceCacheInterface} interface.
     */
    const SOURCE_CACHE = 'app.source_manager';

    /**
     * Feed fetcher factory.
     *
     * Implements {@see \CacheBundle\Feed\Fetcher\Factory\FeedFetcherFactoryInterface}
     * interface.
     */
    const FEED_FETCHER_FACTORY = 'cache.feed_fetcher_factory';

    /**
     * Comment manager.
     *
     * Implements {@see \CacheBundle\Document\Extractor\DocumentContentExtractorInterface}
     * interface.
     */
    const DOCUMENT_CONTENT_EXTRACTOR = 'cache.document_content_extractor';
}
