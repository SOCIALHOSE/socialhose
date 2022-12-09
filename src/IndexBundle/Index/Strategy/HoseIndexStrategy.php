<?php

namespace IndexBundle\Index\Strategy;

use Common\Enum\FieldNameEnum;
use Common\Enum\LanguageEnum;
use Common\Enum\PublisherTypeEnum;
use Common\Util\Converter\DateConverter;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\Model\ArticleDocument;

/**
 * Class HoseIndexStrategy
 *
 * @package IndexBundle\Index\Strategy
 */
class HoseIndexStrategy implements IndexStrategyInterface
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
        'reddit' => 'https?://[^/]*reddit',
    ];

    /**
     * Required fields types.
     *
     * @var array
     */
    private static $fieldsConfig = [
        'sequence' => 'string',
        'date_found' => 'date',
        'source_hashcode' => 'string',
        'source_link' => 'string',
        'source_publisher_type' => 'string',
        'source_publisher_subtype' => 'string',
        'source_date_found' => 'string',
        'source_spam_probability' => 'float',
        'source_assigned_tags' => 'array',
        'source_title' => 'string',
        'source_favorites' => 'integer',
        'source_followers' => 'integer',
        'source_following' => 'integer',
        'source_verified' => 'boolean',
        'source_profiles' => 'array',
        'source_tags' => 'array',
        'source_likes' => 'integer',
        'source_location' => 'string',
        'permalink' => 'string',
        'main' => 'string',
        'main_length' => 'integer',
        'title' => 'string',
        'publisher' => 'string',
        'mentions' => 'array',
        'section' => 'string',
        'tags' => 'array',
        'published' => 'date',
        'author_name' => 'string',
        'author_link' => 'string',
        'author_gender' => 'string',
        'geo_country' => 'string',
        'geo_state' => 'string',
        'geo_city' => 'string',
        'geo_point' => 'string',
        'image_src' => 'string',
        'sentiment' => 'string',
        'extract' => 'string',
        'lang' => 'string',
        'categories' => 'array',
        'duplicates_count' => 'integer',
        'likes' => 'integer',
        'dislikes' => 'integer',
        'comments' => 'integer',
        'views' => 'integer',
        'shares' => 'integer',
        'video_player' => 'string',
        'video_player_width' => 'integer',
        'video_player_height' => 'integer',
        'domain' => 'string',
    ];

    private static $indexableFields = [
        'sequence',
        'date_found',
        'source_hashcode',
        'source_link',
        'source_publisher_type',
        'source_publisher_subtype',
        'source_spam_probability',
        'source_title',
        'source_favorites',
        'source_followers',
        'source_following',
        'source_verified',
        'source_profiles',
        'source_tags',
        'source_likes',
        'source_location',
        'permalink',
        'main',
        'title',
        'publisher',
        'mentions',
        'section',
        'tags',
        'published',
        'author_name',
        'author_link',
        'author_gender',
        'geo_country',
        'geo_state',
        'geo_city',
        'image_src',
        'sentiment',
        'lang',
        'categories',
        'duplicates_count',
        'likes',
        'dislikes',
        'comments',
        'views',
        'shares',
        'domain'
    ];

    /**
     * Map between application publisher type and hose publisher type.
     *
     * @var array
     */
    private static $publisherMap = [
        PublisherTypeEnum::UNKNOWN => [ 'UNKNOWN' ],
        PublisherTypeEnum::BLOGS => [ 'WEBLOG'],
        PublisherTypeEnum::FORUMS => [ 'FORUM', 'MEMETRACKER', 'UNKNOWN' ],
        PublisherTypeEnum::NEWS => [ 'MAINSTREAM_NEWS'],
        PublisherTypeEnum::SOCIAL => [ 'SOCIAL_MEDIA', 'UNKNOWN' ],
        PublisherTypeEnum::VIDEO => [ 'VIDEO', 'UNKNOWN' ],
        PublisherTypeEnum::PHOTO => [ 'PHOTO', 'UNKNOWN' ],
    ];

    /**
     * Map between hose publisher type and application publisher type.
     *
     * @var array
     */
    private static $reversePublisherMap = [
        'UNKNOWN' => PublisherTypeEnum::UNKNOWN,
        'CLASSIFIED' => PublisherTypeEnum::NEWS,
        'WEBLOG' => PublisherTypeEnum::BLOGS,
        'MICROBLOG' => PublisherTypeEnum::BLOGS,
        'FORUM' => PublisherTypeEnum::FORUMS,
        'MEMETRACKER' => PublisherTypeEnum::FORUMS,
        'MAINSTREAM_NEWS' => PublisherTypeEnum::NEWS,
        'REVIEW' => PublisherTypeEnum::NEWS,
        'SOCIAL_MEDIA' => PublisherTypeEnum::SOCIAL,
        'VIDEO' => PublisherTypeEnum::VIDEO,
        'PHOTO' => PublisherTypeEnum::PHOTO,
    ];

    /**
     * Name of the fields which have 'raw' fields.
     *
     * @var array
     */
    public static $rawFieldNameMap = [
        FieldNameEnum::SOURCE_TITLE,
        FieldNameEnum::SECTION,
        FieldNameEnum::AUTHOR_NAME,
        FieldNameEnum::PUBLISHER,
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
        return new ArticleDocument($this, $data);
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
        $normalized = [];

        //
        // Insure that all required field are exists and has values 'cause
        // hose don't worry about data that contains in they index so we should
        // do that ....
        //
        foreach (self::$fieldsConfig as $field => $type) {
            $value = isset($rawData[$field]) ? $rawData[$field] : null;

            switch ($type) {
                case 'array':
                    $value = array_filter((array) $value);
                    break;

                case 'date':
                    $value = DateConverter::convert($value);
                    if ($value === false) {
                        $value = null;
                    }

                    //
                    // hose may contains data from future so we should use
                    // current date instead of that dates.
                    //
                    $now = date_create();
                    if (($value instanceof \DateTimeInterface) && ($value > $now)) {
                        $value = $now;
                    }

                    break;

                default:
                    if ($value !== null) {
                        settype($value, $type);
                        if ($type === 'string') {
                            $value = trim($value);
                        }
                    }
            }

            $normalized[$field] = $value;
        }

        //
        // Insure that we have 'comments' fields.
        //
        $comments = [];
        $commentsCount = 0;
        if (isset($rawData['__comments'])) {
            $comments = $rawData['__comments'];
            $commentsCount = $rawData['__commentsCount'];
        }

        //
        // Some documents in hose index don't contains 'source_publisher_type'
        // so we assume that this document has UNKNOWN type.
        //
        $sourcePublisherType = trim($normalized['source_publisher_type']) === ''
            ? PublisherTypeEnum::UNKNOWN
            : $normalized['source_publisher_type'];

        return [
            'id' => $normalized['sequence'],
            'type' => 'document',
            'title' => $normalized['title'],
            'permalink' => $normalized['permalink'],
            'dateFound' => $normalized['date_found'],
            'published' => $normalized['published'],
            'content' => $this->normalizeContent($normalized),
            //
            // Some documents don't have language field so we assume that it english.
            //
            'language' => trim($normalized['lang']) === '' ? LanguageEnum::ENGLISH : $normalized['lang'],
            'publisher' => $normalized['publisher'],
            'source' => [
                'id' => $normalized['source_hashcode'],
                'title' => $this->normalizeSourceTitle($normalized),
                'type' => $this->normalizePublisherType($sourcePublisherType),
                'link' => $normalized['source_link'],
                'section' => $normalized['section'],
                'country' => $normalized['geo_country'],
                'state' => $normalized['geo_state'],
                'city' => $normalized['geo_city'],
                'siteType' => $this->determineSiteType($normalized['source_link']),
            ],
            'author' => [
                'name' => $normalized['author_name'],
                'link' => $normalized['author_link'],
            ],
            'duplicates' => $normalized['duplicates_count'],
            'image' => $normalized['image_src'],
            'views' => $normalized['views'],
            'sentiment' => $normalized['sentiment'],
            'comments' => $comments,
            'commentsCount' => $commentsCount,
            'domain' =>$normalized['domain']
        ];
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
        $indexableData = [
            FieldNameEnum::PLATFORM => 'hose',
            'main' => $this->normalizeContent($rawData),
            'source_publisher_type' => $this->denormalizePublisherType($rawData['source_publisher_type'])[0],
            'source_title' => $this->normalizeSourceTitle($rawData),
        ];

        if (isset($rawData[FieldNameEnum::COLLECTION_ID])) {
            $indexableData[FieldNameEnum::COLLECTION_ID] = $rawData[FieldNameEnum::COLLECTION_ID];
            $indexableData[FieldNameEnum::COLLECTION_TYPE] = $rawData[FieldNameEnum::COLLECTION_TYPE];
        }

        if (isset($rawData[FieldNameEnum::DELETE_FROM])) {
            $indexableData[FieldNameEnum::DELETE_FROM] = $rawData[FieldNameEnum::DELETE_FROM];
        } else {
            $indexableData[FieldNameEnum::DELETE_FROM] = [];
        }

        foreach (self::$indexableFields as $field) {
            if (isset($rawData[$field])) {
                $indexableData[$field] = $rawData[$field];
            }
        }

        return $indexableData;
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

        return $applicationFieldName;
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
        if (array_key_exists($indexPublisherType, self::$publisherMap)) {
            return $indexPublisherType;
        }

        if (! array_key_exists($indexPublisherType, self::$reversePublisherMap)) {
            throw new \UnexpectedValueException(sprintf(
                'Unhandled index publisher type \'%s\'',
                $indexPublisherType
            ));
        }

        return self::$reversePublisherMap[$indexPublisherType];
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
        if (array_key_exists($applicationPublisherType, self::$reversePublisherMap)) {
            return [ $applicationPublisherType, 'UNKNOWN' ];
        }

        if (! array_key_exists($applicationPublisherType, self::$publisherMap)) {
            throw new \UnexpectedValueException(sprintf(
                'Unhandled application publisher type \'%s\'',
                $applicationPublisherType
            ));
        }

        return self::$publisherMap[$applicationPublisherType];
    }

    /**
     * @param array $rawData Raw data from hose.
     *
     * @return string
     */
    private function normalizeContent(array $rawData)
    {
        if (isset($rawData['main'])) {
            $main = $rawData['main'];
        } elseif (isset($rawData['extract'])) {
            $main = $rawData['extract'];
        } else {
            return '';
        }

        //
        // Replace html entities before any normalization steps.
        //
        $main = html_entity_decode($main);

        //
        // Replace some html tags with new line symbols.
        //
        $main = preg_replace([
            '#<br/?>#i',
            '#</(p|div|section|main|article)>#i',
        ], "\n", $main);

        //
        // Remove links, scripts, styles, images and etc.
        //
        $main = preg_replace([
            '#<(script|style|a)>#i',
            '#<img[^>]*?>#',
        ], '', $main);

        $main = strip_tags($main);

        return trim(preg_replace([
            '# {2,}#',  // Replace multiple consecutive spaces by one.
            '#\n{2,}#', // Replace multiple consecutive 'new line' symbols by one.
            '#\s{2,}#', // At last replace all multiple consecutive of 'empty'
            // symbols by 'new line'.
        ], [
            '',
            "\n",
            "\n",
        ], $main));
    }

    /**
     * @param array $data Normalized data.
     *
     * @return string|null
     */
    private function normalizeSourceTitle(array $data)
    {
        $title = null;

        switch (true) {
            case isset($data['source_feed_title']) && trim($data['source_feed_title']) !== '':
                $title = $data['source_feed_title'];
                break;

            case isset($data['source_title']) && trim($data['source_title']) !== '':
                $title = $data['source_title'];
                break;

            case isset($data['source_resource']) && trim($data['source_resource']) !== '':
                $title = $data['source_resource'];
                break;

            case isset($data['source_link']) && trim($data['source_link']) !== '':
                $title = $data['source_link'];
                break;
        }

        return $title;
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
