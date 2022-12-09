<?php

namespace AppBundle\AdvancedFilters;

use Common\Enum\AFSourceEnum;
use Common\Enum\DocumentsAFNameEnum;
use Common\Enum\AFTypeEnum;
use Common\Enum\FieldNameEnum;
use Common\Enum\SourcesAFNameEnum;

/**
 * Class AdvancedFiltersConfig
 * @package AppBundle\AdvancedFilters
 */
class AdvancedFiltersConfig
{

    /**
     * Configuration of advanced filter for sources.
     *
     * @var array[]
     */
    private static $configs = [
        //
        // Configuration of advanced filter for documents.
        //
        AFSourceEnum::FEED => [
            DocumentsAFNameEnum::ADDITIONAL_QUERY => [
                'type' => AFTypeEnum::QUERY,
                'description' => 'Additional specifying query.',
                'field_name' => '',
                'names' => [
                    FieldNameEnum::MAIN,
                    FieldNameEnum::TITLE,
                ],
            ],
            DocumentsAFNameEnum::SOURCE => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::SOURCE_TITLE,
                'description' => 'Filter documents by source title.',
            ],
            DocumentsAFNameEnum::ARTICLE_DATE => [
                'type' => AFTypeEnum::RANGE,
                'field_name' => FieldNameEnum::PUBLISHED,
                'ranges' => [
                    '15 Minutes' => [ 'from' => 'now-15m', 'key' => '15 Minutes' ],
                    '30 Minutes' => [ 'from' => 'now-30m', 'key' => '30 Minutes' ],
                    '1 Hour' => [ 'from' => 'now-1H', 'key' => '1 Hour' ],
                    '24 Hour' => [ 'from' => 'now-1d', 'key' => '24 Hour' ],
                    '7 Days' => [ 'from' => 'now-7d',  'key' => '7 Days' ],
                ],
                'description' => 'Filter documents by found date.',
            ],
            DocumentsAFNameEnum::SOURCE_COUNTRY   => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::COUNTRY,
                'description' => 'Filter documents by source country.',
            ],
            DocumentsAFNameEnum::SOURCE_STATE     => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::STATE,
                'description' => 'Filter documents by source state.',
            ],
            DocumentsAFNameEnum::SOURCE_CITY      => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::CITY,
                'description' => 'Filter documents by source city.',
            ],
//            DocumentsAFNameEnum::SOURCE_SECTION   => [
//                'type' => AFTypeEnum::SIMPLE,
//                'field_name' => FieldNameEnum::SECTION,
//                'description' => 'Filter documents by source section.',
//            ],
            DocumentsAFNameEnum::ARTICLE_LANGUAGE => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::LANG,
                'description' => 'Filter documents by language.',
            ],
            DocumentsAFNameEnum::AUTHOR           => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::AUTHOR_NAME,
                'description' => 'Filter documents by author name.',
            ],
            DocumentsAFNameEnum::PUBLISHER        => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::PUBLISHER,
                'description' => 'Filter documents by publisher.',
            ],
            DocumentsAFNameEnum::REACH            => [
                'type' => AFTypeEnum::RANGE,
                'field_name' => FieldNameEnum::VIEWS,
                'ranges' => [
                    '0+' => [ 'from' => 0, 'to' => 1000 ],
                    '1000+' => [ 'from' => 1000, 'to' => 5000 ],
                    '5000+' => [ 'from' => 5000, 'to' => 10000 ],
                    '10000+' => [ 'from' => 10000, 'to' => 25000 ],
                    '25000+' => [ 'from' => 25000, 'to' => 50000 ],
                    '50000+' => [ 'from' => 50000, 'to' => 100000 ],
                    '100000+' => [ 'from' => 100000, 'to' => 250000 ],
                    '250000+' => [ 'from' => 250000, 'to' => 500000 ],
                    '500000+' => [ 'from' => 500000, 'to' => 1000000 ],
                    '1000000+' => [ 'from' => 1000000, 'to' => 2500000 ],
                    '2500000+' => [ 'from' => 2500000, 'to' => 5000000 ],
                    '5000000+' => [ 'from' => 5000000, 'to' => 10000000 ],
                    '10000000+' => [ 'from' => 10000000 ],
                ],
                'description' => 'Filter documents by views count.',
            ],
            DocumentsAFNameEnum::SENTIMENT        => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::SENTIMENT,
                'description' => 'Filter documents by sentiment.',
            ],
        ],
        //
        // Configuration of advanced filter for sources.
        //
        AFSourceEnum::SOURCE => [
            SourcesAFNameEnum::LANG => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::LANG,
                'description' => 'Filter sources by language.',
            ],
            SourcesAFNameEnum::COUNTRY => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::COUNTRY,
                'description' => 'Filter sources by country.',
            ],
            SourcesAFNameEnum::STATE => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::STATE,
                'description' => 'Filter sources by state.',
            ],
            SourcesAFNameEnum::CITY => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::CITY,
                'description' => 'Filter sources by city.',
            ],
            SourcesAFNameEnum::SECTION => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::SECTION,
                'description' => 'Filter sources by section.',
            ],
            SourcesAFNameEnum::MEDIA_TYPE => [
                'type' => AFTypeEnum::SIMPLE,
                'field_name' => FieldNameEnum::SOURCE_PUBLISHER_TYPE,
                'description' => 'Filter sources by media type.',
            ],
        ],
    ];

    /**
     * Get configuration for specified document's source.
     *
     * @param string $name One of available constants from AFSourceEnum.
     *
     * @return array[]
     *
     * @see AFSourceEnum
     */
    public static function getConfig($name)
    {
        if (! isset(self::$configs[$name])) {
            throw new \InvalidArgumentException("Unknown config '{$name}'");
        }

        return self::$configs[$name];
    }

    /**
     * Get default value for specified source.
     *
     * @param string $name One of available constants from AFSourceEnum.
     *
     * @return array[]
     *
     * @see AFSourceEnum
     */
    public static function getDefault($name)
    {
        switch ($name) {
            case AFSourceEnum::FEED:
                return [
                    DocumentsAFNameEnum::SOURCE => [ 'data' => [] ],
                    DocumentsAFNameEnum::ARTICLE_DATE => [ 'data' => [] ],
                    DocumentsAFNameEnum::SOURCE_COUNTRY => [ 'data' => [] ],
                    DocumentsAFNameEnum::SOURCE_STATE => [ 'data' => [] ],
                    DocumentsAFNameEnum::SOURCE_CITY => [ 'data' => [] ],
//                    DocumentsAFNameEnum::SOURCE_SECTION => [ 'data' => [] ],
                    DocumentsAFNameEnum::ARTICLE_LANGUAGE => [ 'data' => [] ],
                    DocumentsAFNameEnum::AUTHOR => [ 'data' => [] ],
                    DocumentsAFNameEnum::PUBLISHER => [ 'data' => [] ],
                    DocumentsAFNameEnum::REACH => [ 'data' => [] ],
                    DocumentsAFNameEnum::SENTIMENT => [ 'data' => [] ],
                ];

            case AFSourceEnum::SOURCE:
                return [
                    SourcesAFNameEnum::LANG => [ 'data' => [] ],
                    SourcesAFNameEnum::COUNTRY => [ 'data' => [] ],
                    SourcesAFNameEnum::STATE => [ 'data' => [] ],
                    SourcesAFNameEnum::CITY => [ 'data' => [] ],
                    SourcesAFNameEnum::SECTION => [ 'data' => [] ],
                    SourcesAFNameEnum::MEDIA_TYPE => [ 'data' => [] ],
                ];

            default:
                throw new \InvalidArgumentException('Unknown source '. $name);
        }
    }
}
