<?php

namespace IndexBundle\Index\External;

use AppBundle\AdvancedFilters\Aggregator\AFAggregatorInterface;
use AppBundle\AdvancedFilters\Aggregator\CachedAFAggregator;
use IndexBundle\Index\Internal\InternalIndex;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Index\Strategy\HoseIndexStrategy;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class InternalHoseIndex
 *
 * Index which acts as hose index.
 *
 * @package IndexBundle\Index\External
 */
class InternalHoseIndex extends InternalIndex implements
    ExternalIndexInterface
{

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * AbstractElasticsearchIndex constructor.
     *
     * @param CacheItemPoolInterface $cache A CacheItemPoolInterface instance.
     * @param string                 $host  ElasticSearch server host.
     * @param integer                $port  ElasticSearch server port.
     * @param string                 $index Used ElasticSearch index name.
     * @param string                 $type  Used ElasticSearch document type.
     */
    public function __construct(
        CacheItemPoolInterface $cache,
        $host,
        $port,
        $index,
        $type
    ) {
        parent::__construct($host, $port, $index, $type);
        $this->cache = $cache;
    }

    /**
     * Create concrete strategy instance.
     *
     * @return IndexStrategyInterface
     */
    protected function createStrategy()
    {
        return new HoseIndexStrategy();
    }

    /**
     * @return AFAggregatorInterface
     */
    protected function createAggregator()
    {
        return new CachedAFAggregator($this->cache, parent::createAggregator());
    }
}
