<?php

namespace IndexBundle\Index;

use AppBundle\Response\SearchResponse;
use AppBundle\Response\SearchResponseInterface;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use IndexBundle\Aggregation\AggregationFacadeInterface;
use IndexBundle\Aggregation\ElasticsearchFacade;
use IndexBundle\Aggregation\Factory\AggregationFactory;
use IndexBundle\Aggregation\Resolver\AggregationTypeResolverInterface;
use IndexBundle\Aggregation\Resolver\ElasticserachAggregationTypeResolver;
use IndexBundle\Filter\FilterInterface;
use IndexBundle\Filter\Resolver\ElasticSearchFilterResolver;
use IndexBundle\Filter\Resolver\FilterResolverInterface;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Class AbstractElasticSearchIndex
 *
 * @package IndexBundle\Index
 */
abstract class AbstractElasticSearchIndex extends AbstractIndex
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $index;

    /**
     * @var string
     */
    protected $type;

    /**
     * AbstractElasticsearchIndex constructor.
     *
     * @param string  $host  ElasticSearch server host.
     * @param integer $port  ElasticSearch server port.
     * @param string  $index Used ElasticSearch index name.
     * @param string  $type  Used ElasticSearch document type.
     */
    public function __construct($host, $port, $index, $type)
    {
        $this->client = ClientBuilder::create()
            ->setHosts([
                [
                    'host' => $host,
                    'port' => $port,
                ],
            ])
            ->build();
        $this->index = $index;
        $this->type = $type;
    }

    /**
     * Search information in index.
     *
     * @param SearchRequestInterface $request Internal representation of search
     *                                        request.
     *
     * @return SearchResponseInterface
     */
    public function search(SearchRequestInterface $request)
    {
        $limit = $this->normalizeLimit($request->getLimit());
        $page = $this->normalizePage($request->getPage(), $limit);

        $parameters = $this->buildSearchParameters($request);

        //
        // Limit query and set offset if it necessary.
        //
        if ($limit !== null) {
            $parameters['size'] = $limit;
            if ($page) {
                $parameters['from'] = ($page - 1) * $limit;
            }
        }

        try {
            return $this->normalize($this->client->search($this->beforeSearch($parameters)));
        } catch (\Exception $exception) {
            throw new \RuntimeException(sprintf(
                'Can\'t exec search request \'%s\'. %s',
                json_encode($parameters),
                $exception->getMessage()
            ));
        }
    }

    /**
     * Fetch all relevant documents.
     *
     * @param SearchRequestInterface $request Internal representation of search
     *                                        request.
     *
     * @return \Traversable
     */
    public function fetchAll(SearchRequestInterface $request)
    {
        //
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-scroll.html
        //

        $parameters = $this->buildSearchParameters($request);
        $parameters['scroll'] = '10m';
        $parameters['size'] = $this->normalizeLimit($request->getLimit());

        $response = $this->client->search($this->beforeSearch($parameters));
        $scrollId = $response['_scroll_id'];

        while (count($response['hits']['hits']) > 0) {
            $response = $this->normalize($response);
            gc_collect_cycles();
            yield $response;

            $response = $this->client->scroll([
                'scroll_id' => $scrollId,
                'scroll' => '10m',
            ]);
            $scrollId = $response['_scroll_id'];
        }
    }

    /**
     * Fetch all relevant documents.
     *
     * @param SearchRequestInterface $request Internal representation of search
     *                                        request.
     *
     * @return integer
     */
    public function getTotal(SearchRequestInterface $request)
    {
        $parameters = $this->buildSearchParameters($request);
        $response = $this->client->count($this->beforeSearch($parameters));
        return $response['count'];
    }

    /**
     * Get documents by it ids.
     *
     * @param integer|integer[] $ids    Array of document ids or single id.
     * @param string|string[]   $fields Array of requested fields of single
     *                                  field.
     *
     * @return DocumentInterface[]
     */
    public function get($ids, $fields = [])
    {
        $ids = (array) $ids;
        $fields = (array) $fields;

        $parameters = [
            'body' => [ 'query' => [ 'ids' => [ 'values' => $ids ] ] ],
            'index' => $this->index,
            'type' => $this->type,
            'size' => count($ids),
        ];

        if (count($fields) > 0) {
            $parameters['_source'] = $fields;
        }

        return $this
            ->normalize($this->client->search($this->beforeSearch($parameters)))
            ->getDocuments();
    }

    /**
     * Check that specified documents is exists.
     *
     * @param integer|array $ids Array of document ids or single id.
     *
     * @return array Contains all ids which not found in index.
     */
    public function has($ids)
    {
        //
        // We should insure that all ids has string type for comparision.
        //
        $ids = \nspl\a\map(\nspl\op\str, (array) $ids);

        $response = $this->client->search([
            'body' => [ 'query' => [ 'ids' => [ 'values' => $ids ] ] ],
            'index' => $this->index,
            'type' => $this->type,
            '_source' => false,
        ]);

        // Get founded ids.
        $founded = \nspl\a\map(\nspl\op\itemGetter('_id'), $response['hits']['hits']);

        return array_diff($ids, $founded);
    }

    /**
     * Get aggregation factory instance
     *
     * @return AggregationFactory
     *
     * todo make something with that
     */
    public function getAggregationFactory()
    {
        return new AggregationFactory();
    }

    /**
     * Get aggregation instance
     *
     * @return AggregationFacadeInterface
     *
     * todo make something with that
     */
    public function getAggregation()
    {
        return new ElasticsearchFacade();
    }

    /**
     * Create concrete filter resolver instance.
     *
     * @return FilterResolverInterface
     */
    protected function createFilterResolver()
    {
        return new ElasticSearchFilterResolver($this->getStrategy());
    }

    /**
     * Build elastic search query.
     *
     * @param SearchRequestInterface $request A internal representation of
     *                                        search query.
     *
     * @return array
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html
     */
    protected function buildQuery(SearchRequestInterface $request)
    {
        $query = trim($request->getQuery());
        $buildedQuery = '';

        if ($query !== '') {
            //
            // Denormalize field names.
            //
            $fields = \nspl\a\map(function ($fieldName) {
                return $this->getStrategy()->denormalizeFieldName($fieldName);
            }, $request->getFields());

            if (is_array($fields) && (count($fields) > 0)) {
                //
                // Build search query from provided keywords.
                //
                $buildedQuery = implode(' OR ', array_map(function ($fieldName) use ($query) {
                    return "{$fieldName}:({$query})";
                }, $fields));
            }
        }

        //
        // Build filters
        //
        $filters = $request->getFilters();

        if (count($filters) > 0) {
            $resolver = function (FilterInterface $filter) {
                $resolved = $filter->resolve($this->getFilterResolver());

                if ($resolved !== '') {
                    return "({$resolved})";
                }

                return '';
            };

            $filters = implode(' AND ', array_map($resolver, $filters));
            if ($buildedQuery) {
                $buildedQuery = "({$buildedQuery})";
            }
            if ($filters) {
                $buildedQuery .= $buildedQuery !== '' ? " AND ({$filters})" : "{$filters}";
            }
        }

        return $buildedQuery;
    }

    /**
     * @param array $normalized Normalized document data.
     * @param array $raw        Raw document data.
     *
     * @return array
     */
    protected function additionalNormalization(array $normalized, array $raw)
    {
        if (! isset($normalized['sequence'])) {
            //
            // Use id provided by Elastic if hose not return sequence field.
            // Just in case.
            //
            $normalized['sequence'] = $raw['_id'];
        }

        return $normalized;
    }

    /**
     * @return AggregationTypeResolverInterface
     */
    protected function getAggregationResolver()
    {
        return new ElasticserachAggregationTypeResolver($this->getStrategy());
    }

    /**
     * @param array $parameters A builded parameters.
     *
     * @return array
     */
    protected function beforeSearch(array $parameters)
    {
        return $parameters;
    }

    /**
     * Build proper parameters for ElasticSearch '_search' method.
     *
     * @param SearchRequestInterface $request A search request instance.
     *
     * @return array
     */
    private function buildSearchParameters(SearchRequestInterface $request)
    {
        $parameters = [
            'body' => [],
            'index' => $this->index,
            'type' => $this->type,
        ];

        //
        // Set fields which should be fetched or get all available fields.
        //
        $sources = $request->getSources();
        if (count($sources) > 0) {
            $parameters['_source'] = array_map([ $this->getStrategy(), 'denormalizeFieldName'], $sources);
        }

        //
        // Build 'query_string' conditions.
        //
        $buildedQuery = $this->buildQuery($request);
        if ($buildedQuery !== '') {
            $parameters['body'] = [
                'query' => [
                    'query_string' => [
                        'query' => $buildedQuery,
                    ],
                ],
            ];
        } else {
            //
            // Fetch all documents.
            //
            // https://github.com/elastic/elasticsearch-php/issues/495
            // Convert array to object.
            //
            $parameters['body'] = [ 'query' => [ 'match_all' => (object) [] ] ];
        }

        //
        // Build aggregations.
        //
        $aggregation = $this->buildAggregation($request);
        if (is_array($aggregation) && count($aggregation)) {
            $parameters['body']['aggs'] = $aggregation;
        }

        //
        // Build sorting conditions.
        //
        $sort = $request->getSorts();
        if (count($sort) > 0) {
            //
            // Normalize sorted fields names.
            //
            $normalized = [];
            foreach ($sort as $field => $direction) {
                //
                // Denormalize field name used for sorting.
                //
                $normalized[$this->getStrategy()->denormalizeFieldName($field, true)] = [ 'order' => $direction ];
            }
            $parameters['body']['sort'] = $normalized;
        }

        return $parameters;
    }

    /**
     * @param integer|null $limit Current limit.
     *
     * @return integer
     */
    private function normalizeLimit($limit)
    {
        if (($limit === null) || ($limit > IndexInterface::MAX_RESULT_COUNT)) {
            $limit = self::MAX_RESULT_COUNT;
        }

        if ($limit > self::MAX_RESULT_COUNT) {
            $limit = self::MAX_RESULT_COUNT;
        }

        return $limit;
    }

    /**
     * @param integer $page  Current page.
     * @param integer $limit Normalized limit.
     *
     * @return integer
     */
    private function normalizePage($page, $limit)
    {
        if (($page - 1) * $limit > self::MAX_RESULT_COUNT) {
            $page = (int) floor(self::MAX_RESULT_COUNT / $limit);
        }

        return $page;
    }

    /**
     * Build elastic search aggregation
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     *
     * @return array
     */
    private function buildAggregation(SearchRequestInterface $request)
    {
        $aggregations = $request->getAggregation();

        if ($aggregations === null) {
            return [];
        }
        $result = [];

        foreach ($aggregations as $aggregation) {
            $aggregation = $aggregation->build($this->getAggregationResolver());
            $result[key($aggregation)] = current($aggregation);
        }

        return $result;
    }

    /**
     * @param array $response Response from ElasticSearch server.
     *
     * @return SearchResponse
     */
    private function normalize(array $response)
    {
        $totalCount = $response['hits']['total'];
        $data = $response['hits']['hits'];

        $data = array_map(function (array $document) {
            $result = $document['_source'];

            //
            // Copy ElasticSearch id as id for document. Concrete normalizer may
            // use or not use this field.
            //
            $document['_source']['id'] = $document['_id'];

            //
            // Additionally fetch some data.
            //
            $result = $this->additionalNormalization($result, $document);

            return $this->getStrategy()->createDocument($result);
        }, $data);

        return new SearchResponse(
            $data,
            $this->normalizeAggr($response),
            ($totalCount > self::MAX_RESULT_COUNT) ? self::MAX_RESULT_COUNT : $totalCount
        );
    }

    /**
     * Normalize aggregation results.
     *
     * @param array $data Raw data from ElasticSearch.
     *
     * @return array
     */
    private function normalizeAggr(array $data)
    {
        if (! isset($data['aggregations'])) {
            //
            // We don't have any aggregation results.
            //
            return [];
        }

        return $this->doAggrNormalization($data['aggregations']);
    }

    /**
     * @param array $rawResult Raw aggregation results.
     *
     * @return array
     */
    private function doAggrNormalization(array $rawResult)
    {
        $normalized = [];

        foreach ($rawResult as $name => $result) {
            if (isset($result['buckets'])) {
                $result = $result['buckets'];

                foreach ($result as $bucket) {
                    $data = [
                        'value' => $bucket['key'],
                        'count' => $bucket['doc_count'],
                    ];

                    unset($bucket['key'], $bucket['doc_count']);
                    //
                    // Normalize sub aggregation.
                    //
                    $data['sub'] = $this->doAggrNormalization($bucket);

                    $normalized[$name][] = $data;
                }
            } elseif (isset($result['hits'])) {
                $result = $result['hits']['hits'];

                foreach ($result as $bucket) {
                    $bucket['_source']['_id'] = $bucket['_id'];
                    $normalized[$name][] = $bucket['_source'];
                }
            }
        }

        return $normalized;
    }
}