<?php

namespace CacheBundle\Entity\Feed;

use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use CacheBundle\Entity\Category;
use CacheBundle\Entity\DocumentCollectionInterface;
use CacheBundle\Entity\DocumentCollectionTrait;
use CacheBundle\Entity\Page;
use Common\Enum\CollectionTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * ClipFeed
 * Contains document clipped from another feeds.
 *
 * @ORM\Entity(repositoryClass="CacheBundle\Repository\ClipFeedRepository")
 */
class ClipFeed extends AbstractFeed implements DocumentCollectionInterface
{

    const READ_LATER = 'Read Later';

    use DocumentCollectionTrait;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="CacheBundle\Entity\Page",
     *     mappedBy="clipFeed",
     *     cascade={ "remove" }
     * )
     */
    private $pages;

    /**
     * Array of normalized actual used filters.
     *
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $filters = [];

    /**
     * Advanced filters in the form in which they came to us from the client.
     *
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    protected $rawFilters = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->pages = new ArrayCollection();
    }

    /**
     * Set filters
     *
     * @param array $filters Array of filters.
     *
     * @return static
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set rawFilters
     *
     * @param array $rawFilters Raw filters.
     *
     * @return static
     */
    public function setRawFilters(array $rawFilters)
    {
        $this->rawFilters = $rawFilters;

        return $this;
    }

    /**
     * Get rawFilters
     *
     * @return array
     */
    public function getRawFilters()
    {
        return $this->rawFilters;
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
        return 'feed-type-clippings';
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
            PropertyMetadata::createString('name', [ 'feed', 'feed_tree' ]),
            PropertyMetadata::createString('subType', [ 'feed', 'feed_tree' ])
                ->setField(function () {
                    return static::getSubType();
                }),
            PropertyMetadata::createString('class', [ 'feed', 'feed_tree' ])
                ->setField(function () {
                    return $this->getSpecificType();
                }),
            PropertyMetadata::createBoolean('exported', [ 'feed', 'feed_tree' ])
                ->setField(function () {
                    return $this->getExported();
                }),
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
     * Add page
     *
     * @param Page $page A page entity instance.
     *
     * @return ClipFeed
     */
    public function addPage(Page $page)
    {
        $this->pages[] = $page;
        $page->setClipFeed($this);

        return $this;
    }

    /**
     * Remove page
     *
     * @param Page $page A Page entity instance.
     *
     * @return ClipFeed
     */
    public function removePage(Page $page)
    {
        $this->pages->removeElement($page);
        $page->setClipFeed(null);

        return $this;
    }

    /**
     * Get pages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Create proper Page entity instance for binding document and current
     * collection.
     *
     * @param integer $number Page number.
     *
     * @return Page
     */
    public function createPage($number)
    {
        return Page::create()
            ->setClipFeed($this)
            ->setNumber($number);
    }

    /**
     * @return integer
     */
    public function getCollectionId()
    {
        return $this->id;
    }

    /**
     * @return CollectionTypeEnum
     */
    public function getCollectionType()
    {
        return CollectionTypeEnum::feed();
    }
}
