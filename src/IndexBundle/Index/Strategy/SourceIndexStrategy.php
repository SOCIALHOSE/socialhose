<?php

namespace IndexBundle\Index\Strategy;

use Common\Enum\FieldNameEnum;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\Model\SourceDocument;

/**
 * Class SourceIndexStrategy
 *
 * @package IndexBundle\Index\Strategy
 */
class SourceIndexStrategy implements IndexStrategyInterface
{
    /**
     * @var array
     */
    private static $siteTypesMap = [
        'twitter' => 'https?://[^/]*twitter',
        'facebook' => 'https?://[^/]*facebook',
        'instagram' => 'https?://[^/]*instagram',
        'tumblr' => 'https?://[^/]*tumblr',
        'pinterest' => 'https?://[^/]*pinterest',
        'youtube' => 'https?://[^/]*youtube',
    ];

    /**
     * Map between internal fields name and cache field name.
     *
     * @var array
     */
    private static $fieldNameMap = [
        'id' => FieldNameEnum::SOURCE_HASHCODE,
        'title' => FieldNameEnum::SOURCE_TITLE,
        'type' => FieldNameEnum::SOURCE_PUBLISHER_TYPE,
        'url' => FieldNameEnum::SOURCE_LINK,
        'country' => FieldNameEnum::COUNTRY,
        'city' => FieldNameEnum::CITY,
        'state' => FieldNameEnum::STATE,
    ];

    /**
     * Reverse filed map.
     *
     * @var array
     */
    private static $reversedFieldNameMap = [
        FieldNameEnum::SOURCE_HASHCODE => 'id',
        FieldNameEnum::SOURCE_TITLE => 'title',
        FieldNameEnum::SOURCE_PUBLISHER_TYPE => 'type',
        FieldNameEnum::SOURCE_LINK => 'url',
        FieldNameEnum::COUNTRY => 'country',
        FieldNameEnum::CITY => 'city',
        FieldNameEnum::STATE => 'state',
    ];

    /**
     * Name of the fields which have 'raw' fields.
     *
     * @var array
     */
    public static $rawFieldNameMap = [
        FieldNameEnum::SOURCE_TITLE,
    ];

    /**
     * Create proper document instance.
     *
     * @param array $data Document data fetched from index.
     *
     * @return DocumentInterface
     */
    public function createDocument(array $data)
    {
        return new SourceDocument($this, $data);
    }

    /**
     * Normalized document data.
     *
     * @param array $rawData Raw document data.
     *
     * @return array
     * @internal
     */
    public function normalizeDocumentData(array $rawData)
    {
        $rawData['type'] = $this->normalizePublisherType($rawData['type']);
        $rawData['siteType'] = $this->determineSiteType($rawData['url']);

        return $rawData;
    }

    /**
     * Get data which should be used for indexing.
     *
     * @param array $rawData Raw document data.
     *
     * @return array
     * @internal
     */
    public function getIndexableData(array $rawData)
    {
        return $rawData;
    }

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
    public function normalizeFieldName($indexFieldName, $fromAggregation = false)
    {
        $applicationFieldName = $indexFieldName;
        if ($fromAggregation && (strpos($indexFieldName, '.raw') !== false)) {
            $applicationFieldName = substr($indexFieldName, 0, -4);
            if ($applicationFieldName === false) {
                throw new \RuntimeException(sprintf(
                    'Can\'t normalize field name from aggregation \'%s\'',
                    $indexFieldName
                ));
            }
        }

        return isset(self::$fieldNameMap[$applicationFieldName])
            ? self::$fieldNameMap[$applicationFieldName]
            : $applicationFieldName;
    }

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
    public function denormalizeFieldName($applicationFieldName, $forAggregation = false)
    {
        $indexFieldName = $applicationFieldName;
        if (isset(self::$reversedFieldNameMap[$applicationFieldName])) {
            $indexFieldName = self::$reversedFieldNameMap[$applicationFieldName];
        }

        if ($forAggregation && in_array($applicationFieldName, self::$rawFieldNameMap, true)) {
            $indexFieldName .= '.raw';
        }

        return $indexFieldName;
    }

    /**
     * Convert concrete publisher type from index into application level type.
     *
     * @param string $indexPublisherType Publisher type from index.
     *
     * @return string
     */
    public function normalizePublisherType($indexPublisherType)
    {
        return $indexPublisherType;
    }

    /**
     * Convert application level publisher type into type for concrete index.
     *
     * @param string $applicationPublisherType Application publisher type.
     *
     * @return string[]
     */
    public function denormalizePublisherType($applicationPublisherType)
    {
        return [ $applicationPublisherType ];
    }

    /**
     * @param string $link Source link.
     *
     * @return string
     */
    private function determineSiteType($link)
    {
        foreach (self::$siteTypesMap as $type => $regexp) {
            if (preg_match("#{$regexp}#i", $link) === 1) {
                return $type;
            }
        }

        return null;
    }
}
