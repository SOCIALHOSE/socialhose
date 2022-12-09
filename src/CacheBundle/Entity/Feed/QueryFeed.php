<?php

namespace CacheBundle\Entity\Feed;

use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use CacheBundle\Entity\Category;
use CacheBundle\Entity\Query\StoredQuery;
use Common\Enum\CollectionTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * QueryFeed
 * Feed which create from stored query.
 *
 * @ORM\Entity(repositoryClass="CacheBundle\Repository\QueryFeedRepository")
 */
class QueryFeed extends AbstractFeed
{

    /**
     * @var StoredQuery
     *
     * @ORM\ManyToOne(
     *     targetEntity="CacheBundle\Entity\Query\StoredQuery",
     *     inversedBy="feeds"
     * )
     */
    protected $query;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $publisherTypes;

    /**
     * Set query
     *
     * @param StoredQuery $query A StoredQuery entity instance.
     *
     * @return QueryFeed
     */
    public function setQuery(StoredQuery $query = null)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return StoredQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set publisherTypes
     *
     * @param array|string $publisherTypes Query publisher type.
     *
     * @return QueryFeed
     */
    public function setPublisherTypes($publisherTypes)
    {
        $this->publisherTypes = (array) $publisherTypes;

        return $this;
    }

    /**
     * Get publisherTypes
     *
     * @return array
     */
    public function getPublisherTypes()
    {
        return $this->publisherTypes;
    }

    /**
     * Get specific feed type.
     *
     * Used by frontend.
     *
     * @return string
     */
    public function getSpecificType()
    {
        if (is_array($this->publisherTypes) && count($this->publisherTypes) === 1) {
            return 'feed-type-'
                . strtolower(current($this->publisherTypes));
        }

        return 'feed-type-mixed';
    }

    /**
     * Return metadata for current entity.
     *
     * @return Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class, [
            PropertyMetadata::createInteger('id', [ 'id' ]),
            PropertyMetadata::createInteger('query', [ 'feed', 'feed_tree' ])
                ->setField(function () {
                    return $this->query->getId();
                }),
            PropertyMetadata::createString('name', [ 'feed', 'feed_tree' ]),
            PropertyMetadata::createString('subType', [ 'feed', 'feed_tree' ])
                ->setField(function () {
                    return static::getSubType();
                }),
            PropertyMetadata::createString('class', [ 'feed', 'feed_tree' ])
                ->setField(function () {
                    return $this->getSpecificType();
                }),
            PropertyMetadata::createBoolean('exported', [ 'feed', 'feed_tree' ]),
//                ->setField(function () {
//                    return $this->isExported();
//                }),
            PropertyMetadata::createEntity('category', Category::class, [ 'feed' ]),
            PropertyMetadata::createEntity('user', User::class, [ 'feed' ]),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'feed', 'id' ];
    }

    /**
     * @return integer
     */
    public function getCollectionId()
    {
        return $this->query->getId();
    }

    /**
     * @return CollectionTypeEnum
     */
    public function getCollectionType()
    {
        return CollectionTypeEnum::query();
    }
}
