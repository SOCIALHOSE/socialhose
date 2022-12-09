<?php

namespace AppBundle\AdvancedFilters\Aggregator;

use AppBundle\Entity\CacheItem;
use IndexBundle\SearchRequest\SearchRequestInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class CachedAFAggregator
 *
 * @package AppBundle\AdvancedFilters\Aggregator
 */
class CachedAFAggregator implements AFAggregatorInterface
{

    const LIFETIME = 1800;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var AFAggregatorInterface
     */
    private $internal;

    /**
     * CachedAFAggregator constructor.
     *
     * @param CacheItemPoolInterface $cache    A CacheItemPoolInterface instance.
     * @param AFAggregatorInterface  $internal A AFAggregatorInterface instance.
     */
    public function __construct(
        CacheItemPoolInterface $cache,
        AFAggregatorInterface $internal
    ) {
        $this->cache = $cache;
        $this->internal = $internal;
    }

    /**
     * Return available filters values for specified request.
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     *
     * @return array
     */
    public function getValues(SearchRequestInterface $request)
    {
        $cachedValues = $this->cache->getItem($request->getHash());
        if (! $cachedValues->isHit()) {
            $cachedValues = new CacheItem(
                $request->getHash(),
                $this->internal->getValues($request),
                self::LIFETIME
            );

            $this->cache->save($cachedValues);
        }

        return $cachedValues->get();
    }
}
