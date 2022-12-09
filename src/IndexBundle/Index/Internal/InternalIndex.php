<?php

namespace IndexBundle\Index\Internal;

use AppBundle\AdvancedFilters\Aggregator\AFAggregatorInterface;
use AppBundle\AdvancedFilters\Aggregator\ArticleAFAggregator;
use IndexBundle\Index\AbstractElasticSearchIndex;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Index\Strategy\InternalIndexStrategy;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Class InternalIndex
 *
 * @package IndexBundle\Index\Internal
 */
class InternalIndex extends AbstractElasticSearchIndex implements
    InternalIndexInterface
{

    /**
     * Size of index bucket.
     * Used for sending big request to ES server.
     */
    const BUCKET_SIZE = 25;

    /**
     * Create new index.
     *
     * @param array $mapping  Index mapping.
     * @param array $settings Index settings.
     *
     * @return void
     */
    public function createIndex(array $mapping, array $settings = [])
    {
        //
        // We don't create index with given name because indexing may be very
        // long operations, so we add '_current' prefix and add alias for index.
        //
        // With this configuration we can simple create new index wait until it
        // documents indexing completed and change alias without breaking site
        // functionality.
        //
        $alias = $this->index .'_current';

        // Remove index if it already created.
        $exists = $this->client->indices()->exists([ 'index' => $alias ]);
        if ($exists) {
            $this->client->indices()->delete([ 'index' => $alias ]);
        }

        // Create new actual index.
        $this->client->indices()->create([
            'index' => $alias,
            'body' => [
                'settings' => $settings,
                'mappings' => [
                    $this->type => [
                        '_all' => [ 'enabled' => false ],
                        'properties' => $mapping,
                    ],
                ],
            ],
        ]);

        //
        // Create alias with specified name for new index.
        //
        $this->client->indices()->putAlias([
            'index' => $alias,
            'name' => $this->index,
        ]);
    }

    /**
     * Index given document or array of documents.
     *
     * @param DocumentInterface|DocumentInterface[] $data DocumentInterface instance
     *                                                    or array of instances.
     *
     * @return void
     */
    public function index($data)
    {
        if ($data instanceof DocumentInterface) {
            $data = [ $data ];
        }

        if (! is_array($data) || ! \app\a\allInstanceOf($data, DocumentInterface::class)) {
            throw new \InvalidArgumentException(sprintf(
                'Data should be single %s instance or array of instance',
                DocumentInterface::class
            ));
        }

        //
        // Convert data into proper ES 'bulk' method properties.
        //
        $data = \nspl\a\map(function (DocumentInterface $document) {
            $data = $document->getIndexableData();
            $config = [
                'index' => [
                    '_index' => $this->index,
                    '_type'  => $this->type,
                ],
            ];

            if (isset($data['id'])) {
                $config['index']['_id'] = $data['id'];
            }

            return [
                'config' => $config,
                'document' => $data,
            ];
        }, $data);

        //
        // ElasticSearch has limit for request payload size, for example x4.large
        // on AWS limits up to 10 Mb. So we should split 'index' request to small
        // buckets.
        //
        // See: http://docs.aws.amazon.com/elasticsearch-service/latest/developerguide/aes-limits.html#network-limits
        //
        $buckets = [];
        $bucketsCount = count($data) / self::BUCKET_SIZE;

        for ($i = 0; $i < $bucketsCount; ++$i) {
            $bucket = \nspl\a\drop($data, $i * self::BUCKET_SIZE);
            $bucket = \nspl\a\take($bucket, self::BUCKET_SIZE);

            $buckets[] = $bucket;
        }

        foreach ($buckets as $bucket) {
            $params = [ 'body' => [] ];
            foreach ($bucket as $part) {
                $params['body'][] = $part['config'];
                $params['body'][] = $part['document'];
            }

            $response = $this->client->bulk($params);
            if (!isset($response['errors']) || $response['errors']) {
                throw new \RuntimeException("Can't make bulk index due to " . json_encode($response));
            }
        }
    }

    /**
     * Update specified document.
     *
     * Make partial update so in data must be placed only changed properties.
     *
     * @param string|integer $id   Updated document id.
     * @param array          $data Array of changed data where key is property
     *                             name and value is new property value.
     *
     * @return void
     */
    public function update($id, array $data)
    {
        $this->client->update([
            'id' => $id,
            'index' => $this->index,
            'type' => $this->type,
            'body' => [ 'doc' => $data ],
        ]);
    }

    /**
     * Update array of documents.
     *
     * Make partial update so for each document id we should place only changed
     * property.
     *
     * @param array $config Array of arrays where key is updated document id and
     *                      value is array of updated fields same as $data in
     *                      `update` method.
     *
     * @return void
     */
    public function updateBulk(array $config)
    {
        $params = [ 'body' => [] ];
        foreach ($config as $id => $data) {
            if (! is_array($data)) {
                throw new \InvalidArgumentException("Invalid data for updating {$id}, must be an array of property name and new value.");
            }

            $params['body'][] = [
                'update' => [
                    '_index' => $this->index,
                    '_type' => $this->type,
                    '_id' => $id,
                ],
            ];
            $params['body'][] = $data;
        }

        $response = $this->client->bulk($params);

        if (! isset($response['errors']) || $response['errors']) {
            throw new \RuntimeException("Can't make bulk update due to {$response['errors']}");
        }
    }

    /**
     * Update array of documents with filtering.
     *
     * Make partial update so for each document id we should place only changed
     * property.
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     * @param string                 $script  Updating script.
     * @param array                  $params  Script parameters.
     *
     * @return void
     */
    public function updateByQuery(SearchRequestInterface $request, $script, array $params = [])
    {
        $parameters = [
            'body' => [
                'script' => [
                    'inline' => $script,
                ],
            ],
            'index' => $this->index,
            'type' => $this->type,
        ];

        if (count($params) > 0) {
            $parameters['body']['script']['params'] = $params;
        }

        $buildedQuery = $this->buildQuery($request);
        if ($buildedQuery) {
            $parameters['body']['query'] = [
                'query_string' => [
                    'query' => $buildedQuery,
                ],
            ];
        }

        $this->client->updateByQuery($parameters);
    }

    /**
     * Purge index.
     *
     * @return void
     */
    public function purge()
    {
        $this->client->deleteByQuery([
            'index' => $this->index,
            'type' => $this->type,

            //
            // https://github.com/elastic/elasticsearch-php/issues/495
            // Convert array to object.
            //
            'body' => [ 'query' => [ 'match_all' => (object) [] ] ],
        ]);
    }

    /**
     * Remove document by specified id or array of ids.
     *
     * @param string|string[] $id Document id or array of document ids.
     *
     * @return void
     */
    public function remove($id)
    {
        $this->client->deleteByQuery([
            'index' => $this->index,
            'type' => $this->type,
            'body' => [ 'query' => [ 'ids' => [ 'values' => array_filter((array) $id) ] ] ],
        ]);
    }

    /**
     * Create concrete strategy instance.
     *
     * @return IndexStrategyInterface
     */
    protected function createStrategy()
    {
        return new InternalIndexStrategy();
    }

    /**
     * @return AFAggregatorInterface
     */
    protected function createAggregator()
    {
        return new ArticleAFAggregator($this);
    }
}
