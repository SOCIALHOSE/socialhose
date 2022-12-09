<?php

namespace IndexBundle\Index\Strategy;

use IndexBundle\Model\DocumentInterface;

/**
 * Interface IndexStrategyInterface
 *
 * Strategy used for manipulating index.
 *
 * @package IndexBundle\Index\Strategy
 */
interface IndexStrategyInterface
{

    /**
     * Create proper document instance.
     *
     * @param array $data Document data fetched from index.
     *
     * @return DocumentInterface
     */
    public function createDocument(array $data);

    /**
     * Normalized document data.
     *
     * @param array $rawData Raw document data.
     *
     * @return array
     * @internal
     */
    public function normalizeDocumentData(array $rawData);

    /**
     * Get data which should be used for indexing.
     *
     * @param array $rawData Raw document data.
     *
     * @return array
     * @internal
     */
    public function getIndexableData(array $rawData);

    /**
     * Convert concrete index field name into proper application field name.
     *
     * @param string  $indexFieldName  Field name from index.
     * @param boolean $fromAggregation We got field from aggregation response and
     *                                 should normalize by another rules if true.
     *                                 We need this flag because of some index
     *                                 services like ElasticSearch where some
     *                                 field maybe exists in data without indexing
     *                                 but this field has field which is indexed.
     *
     * @return string
     */
    public function normalizeFieldName($indexFieldName, $fromAggregation = false);

    /**
     * Convert application level field name into field name for concrete index.
     *
     * @param string  $applicationFieldName Application field name.
     * @param boolean $forAggregation       This field will be used in aggregation
     *                                      and we should denormalize by another
     *                                      rules if true. We need this flag
     *                                      because of some index services like
     *                                      ElasticSearch where some field maybe
     *                                      exists in data without indexing but
     *                                      this field has field which is indexed.
     *
     * @return string
     */
    public function denormalizeFieldName($applicationFieldName, $forAggregation = false);

    /**
     * Convert concrete publisher type from index into application level type.
     *
     * @param string $indexPublisherType Publisher type from index.
     *
     * @return string
     */
    public function normalizePublisherType($indexPublisherType);

    /**
     * Convert application level publisher type into type for concrete index.
     *
     * @param string $applicationPublisherType Application publisher type.
     *
     * @return string[] Return array cause one application type maybe equal to
     *                  several index types.
     */
    public function denormalizePublisherType($applicationPublisherType);
}
