<?php

namespace Common\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class LanguageEnum
 * Enum of internal field names.
 *
 * @package Common\Enum
 */
class FieldNameEnum extends AbstractEnum
{

    const SEQUENCE = 'sequence';
    const DATE_FOUND = 'date_found';
    const SOURCE_HASHCODE = 'source_hashcode';
    const SOURCE_LINK = 'source_link';
    const SOURCE_PUBLISHER_TYPE = 'source_publisher_type';
    const SOURCE_PUBLISHER_SUBTYPE = 'source_publisher_subtype';
    const SOURCE_DATE_FOUND = 'source_date_found';
    const SOURCE_TITLE = 'source_title';
    const SOURCE_FEED_TITLE = 'source_feed_title';
    const SOURCE_LOCATION = 'source_location';
    const PERMALINK = 'permalink';
    const MAIN = 'main';
    const TITLE = 'title';
    const PUBLISHER = 'publisher';
    const SECTION = 'section';
    const TAGS = 'tags';
    const LINKS = 'links';
    const PUBLISHED = 'published';
    const AUTHOR_NAME = 'author_name';
    const AUTHOR_LINK = 'author_link';
    const AUTHOR_GENDER = 'author_gender';
    const COUNTRY = 'geo_country';
    const STATE = 'geo_state';
    const CITY = 'geo_city';
    const IMAGE_SRC = 'image_src';
    const SENTIMENT = 'sentiment';
    const LANG = 'lang';
    const DUPLICATES_COUNT = 'duplicates_count';
    const VIEWS = 'views';
    const SHARES = 'shares';
    const DOMAIN= 'domain';

    // Special names.
    const PLATFORM = '__platform';
    const COLLECTION_ID = '__collection_id';
    const COLLECTION_TYPE = '__collection_type';
    const DELETE_FROM = '__deleted_from';
}
