<?php

namespace IndexBundle\Index\External;

use AppBundle\AdvancedFilters\Aggregator\AFAggregatorInterface;
use AppBundle\AdvancedFilters\Aggregator\ArticleAFAggregator;
use AppBundle\AdvancedFilters\Aggregator\CachedAFAggregator;
use Elasticsearch\ClientBuilder;
use IndexBundle\Index\AbstractElasticSearchIndex;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Index\Strategy\HoseIndexStrategy;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

/**
 * Class HoseIndex
 *
 * Index which acts as hose index.
 *
 * @package IndexBundle\Index\External
 */
class HoseIndex extends AbstractElasticSearchIndex implements
    ExternalIndexInterface
{

    const HOT = 'content_*';
    const WARM = 'warm_content_*';
    const COLD = 'cold_content_*';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * AbstractElasticsearchIndex constructor.
     *
     * @param LoggerInterface        $logger A LoggerInterface instance.
     * @param CacheItemPoolInterface $cache  A CacheItemPoolInterface instance.
     * @param string                 $host   ElasticSearch server host.
     * @param string                 $vendor Name of account registered on
     *                                       hose.
     * @param string                 $auth   Authentication token associated
     *                                       with vendor.
     * @param string                 $proxy  Used proxy in 'host:port' format.
     *                                       May be null bu required if hose
     *                                       send 503 on every requests.
     */
    public function __construct(
        LoggerInterface $logger,
        CacheItemPoolInterface $cache,
        $host,
        $vendor,
        $auth,
        $proxy = null
    ) {
        $this->logger = $logger;
        $this->cache = $cache;
        $connectionParams = [
            'client' => [
                'headers' =>  [
                    //
                    // We should set 'host' header explicitly 'cause otherwise
                    // library add port and we got 503 error from hose server.
                    //
                    // Most likely this is a bug of hose but we should handle
                    // it.
                    //
                    'host' => [ 'allcontent.elasticsearch.socialhose.io' ],
                    'X-vendor' => [ $vendor ],
                    'X-vendor-auth' => [ $auth ],
                ],
            ],
        ];

        if ($proxy !== null) {
            //
            // Set proxy 'cause from some servers we always got 503 error from
            // hose.
            //
            // Maybe this issues relative to previous bug with host header ...
            //
            $connectionParams['client']['curl'] = [
                CURLOPT_PROXY => $proxy,
            ];
        }

        $this->client = ClientBuilder::create()
            ->setHosts([
                [
                    'host' => $host,
                    'port' => 80,
                ],
            ])
            ->setConnectionParams($connectionParams)
            ->build();
        $this->type = null;
        $this->index = self::HOT;
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
        return new CachedAFAggregator($this->cache, new ArticleAFAggregator($this));
    }

    /**
     * @param array $parameters A builded parameters.
     *
     * @return array
     */
    protected function beforeSearch(array $parameters)
    {
        $this->logger->info(sprintf(
            'Make search request to hose with parameters \'%s\'',
            json_encode($parameters)
        ));

        $query = $parameters['body']['query']['query_string']['query'];
        $parameters['index'] = $this->determineIndex($this->getQueryRangeEnd($query));

        return $parameters;
    }

    /**
     * @param string $query ElasticSearch 'query_string' query parameter.
     *
     * @return \DateTime|null
     */
    private function getQueryRangeEnd($query)
    {
        $matched = [];
        preg_match_all('/published:\[(.*?) TO .*?\]/i', $query, $matched);

        $endDates = array_filter(array_map('trim', $matched[1]), function ($value) {
            return $value !== '*';
        });

        if (count($endDates) > 0) {
            rsort($endDates);
            return new \DateTime(\nspl\a\first($endDates));
        }

        return null;
    }

    /**
     * Determine used Spinn3 index based on provided filters.
     *
     * @param \DateTime $endDate End date of fetch range.
     *
     * @return string
     *
     * @see http://allcontent.console.datastreamer.io/docs/search-overview.html#hot-warm-cold-architecture-and-archive-content
     */
    private function determineIndex(\DateTime $endDate = null)
    {
        $usedIndices = [ self::HOT, self::WARM, self::COLD ];
        if ($endDate !== null) {
            switch (true) {
                case $endDate <= date_create('+ 30 days 00:00:00'):
                    $usedIndices = [ self::HOT ];
                    break;

                case $endDate <= date_create('+ 60 days 00:00:00'):
                    $usedIndices = [ self::HOT, self::WARM ];
                    break;
            }
        }

        return implode(',', $usedIndices);
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
