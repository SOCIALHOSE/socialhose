<?php

namespace AppBundle\AdvancedFilters\Aggregator;

use Common\Enum\DocumentsAFNameEnum;
use Common\Enum\AFTypeEnum;
use IndexBundle\Aggregation\AggregationInterface;
use IndexBundle\Index\IndexInterface;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Class AbstractElasticSearchAFAggregator
 *
 * Realization of AFAggregatorInterface for Elasticsearch.
 *
 * @package AppBundle\AdvancedFilters\Aggregator
 */
abstract class AbstractElasticSearchAFAggregator implements AFAggregatorInterface
{

    /**
     * @var IndexInterface
     */
    private $index;

    /**
     * ElasticsearchAFAggregator constructor.
     *
     * @param IndexInterface $index A IndexInterface instance.
     */
    public function __construct(IndexInterface $index)
    {
        $this->index = $index;
    }

    /**
     * Return available filters values for specified request.
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     *
     * @return array
     *
     * @see AFSourceEnum
     */
    public function getValues(SearchRequestInterface $request)
    {
        $aggregations = [];
        $AFConfig = $this->getAggregationConfig();

        foreach ($AFConfig as $aggregationName => $config) {
            if ($config['type'] !== AFTypeEnum::QUERY) {
                $aggregations[] = $this
                    ->createAggregation($aggregationName, $config);
            }
        }

        // Create new builder for aggregation only.
        $builder = $this->index->createRequestBuilder();
        $response = $builder
            ->fromSearchRequest($request)
            ->setAggregation($aggregations)
            ->setLimit(0) // We don't need any founded documents, only aggregations
                          // results.
            ->build()
            ->execute();

        // Normalize aggregation results.
        $results = $response->getAggregationResults();
        $values = [];

        foreach ($results as $name => $body) {
            //
            // Normalize concrete filter aggregation.
            //
            // For some reasons ElasticSearch invert aggregation data in buckets
            // when we try to aggregate filed with 'date' type and our custom
            // names from config are assigned to invalid values. So for 'articleDate'
            // filter we invert all values in bucket.
            //
            if ($name === DocumentsAFNameEnum::ARTICLE_DATE) {
                $body = array_reverse($body);
            }

            $values[$name] = [];
            foreach ($body as $value) {
                //
                // Normalize concrete filter aggregation result.
                //
                $valueName = $value['value'];

                $values[$name][] = [
                    'value' => $valueName,
                    'count' => $value['count'],
                ];
            }
        }

        //
        // Get not founded advanced filters values and force it into response.
        //
        $notFounded = array_diff(array_keys($this->getDefaultValue()), array_keys($results));
        foreach ($notFounded as $filter) {
            $values[$filter] = [];
        }

        return $values;
    }

    /**
     * Create new Aggregation instance from specified config.
     *
     * @param string $name   Aggregation name.
     * @param array  $config Aggregation config.
     *
     * @return AggregationInterface
     */
    private function createAggregation($name, array $config)
    {
        $factory = $this->index->getAggregationFactory();
        $aggregation = $this->index->getAggregation();

        // Get aggregation type and convert to proper ElasticSearch aggregation type.
        $type = ($config['type'] === AFTypeEnum::SIMPLE) ? 'terms' : 'range';

        $params = [
            'type' => $type,
            'field_name' => $config['field_name'],
        ];

        if ($type === AFTypeEnum::RANGE) {
            // We should get only ranges without names.
            $params['ranges'] = array_values($config['ranges']);
        }

        // Create new aggregation.
        return $aggregation->getAggregation($name, $factory->{$type}($params));
    }

    /**
     * Aggregation config.
     *
     * @return array
     */
    abstract protected function getAggregationConfig();

    /**
     * Get default value for this aggregation results.
     *
     * @return array
     */
    abstract protected function getDefaultValue();
}
