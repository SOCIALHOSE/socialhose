<?php

namespace IndexBundle\Util\Initializer;

use Common\Enum\FieldNameEnum;
use IndexBundle\Index\Internal\InternalIndexInterface;

/**
 * Class InternalIndexInitializer
 * @package IndexBundle\Util\Initializer
 */
class InternalIndexInitializer extends AbstractIndexInitializer
{

    /**
     * Initialize index mapping.
     *
     * @return void
     */
    public function initializeIndex()
    {
        if ($this->index instanceof InternalIndexInterface) {
            $this->index->createIndex([
                //
                // Application specific.
                //
                FieldNameEnum::PLATFORM => ['type' => 'keyword'],
                FieldNameEnum::COLLECTION_ID => ['type' => 'long'],
                FieldNameEnum::COLLECTION_TYPE => ['type' => 'keyword'],
                FieldNameEnum::DELETE_FROM => ['type' => 'long'],
                //
                // hose specific.
                //
                'sequence' => ['type' => 'long'],
                'date_found' => ['type' => 'date'],
                // Start hose source_*
                'source_hashcode' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
                'source_link' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
                'source_publisher_type' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'source_publisher_subtype' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'source_spam_probability' => ['type' => 'float'],
                'source_title' => [
                    'type' => 'string',
                    'fields' => [
                        'raw' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ],
                    ],
                ],
                'source_favorites' => ['type' => 'integer'],
                'source_followers' => ['type' => 'integer'],
                'source_following' => ['type' => 'integer'],
                'source_verified' => ['type' => 'boolean'],
                'source_profiles' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'source_tags' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'source_likes' => ['type' => 'integer'],
                'source_location' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                // End hose source_*
                'permalink' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'main' => ['type' => 'string'],
                'title' => ['type' => 'string'],
                'publisher' => [
                    'type' => 'string',
                    'norms' => false,
                    'fields' => [
                        'raw' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ],
                    ],
                ],
                'mentions' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'section' => [
                    'type' => 'string',
                    'norms' => false,
                    'fields' => [
                        'raw' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ],
                    ],
                ],
                'tags' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
                'published' => ['type' => 'date'],
                'author_name' => [
                    'type' => 'string',
                    'fields' => [
                        'raw' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ],
                    ],
                ],
                'author_link' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'author_gender' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'geo_country' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                    'fielddata' => true,
                ],
                'geo_state' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'geo_city' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'image_src' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'sentiment' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'lang' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'norms' => false,
                ],
                'categories' => ['type' => 'object'],
                'duplicates_count' => ['type' => 'integer'],
                'likes' => ['type' => 'integer'],
                'dislikes' => ['type' => 'integer'],
                'comments' => ['type' => 'integer'],
                'shares' => ['type' => 'integer'],
                'views' => ['type' => 'integer'],
            ], [
                'number_of_shards' => 4,
            ]);
        } else {
            throw new \LogicException('Can\'t initialize internal connection for '. get_class($this->index));
        }
    }
}
