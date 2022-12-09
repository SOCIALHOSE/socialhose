<?php

namespace CacheBundle\Entity\Analytic;

use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\EntityInterface;
use CacheBundle\Entity\Feed\AbstractFeed;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use IndexBundle\Filter\FilterInterface;

/**
 * AnalyticContext
 *
 * Holds all necessary data for making analytic computation.
 *
 * Created 'cause we may got same analytic request from different users, so we
 * should'nt create two equals analytic.
 *
 * @ORM\Table(name="analytics_context")
 * @ORM\Entity
 */
class AnalyticContext implements
    EntityInterface,
    NormalizableEntityInterface
{

    /**
     * @var string
     *
     * @ORM\Column
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $hash;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="CacheBundle\Entity\Feed\AbstractFeed")
     * @ORM\JoinTable(
     *     name="cross_analytics_feeds",
     *     joinColumns={@ORM\JoinColumn(referencedColumnName="hash",onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="feed_id")}
     * )
     */
    private $feeds;

    /**
     * Array of normalized and actually used filters.
     *
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $filters;

    /**
     * Filters in the form in which they came to us from the client.
     *
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $rawFilters;

    /**
     * @ORM\OneToMany(targetEntity="CacheBundle\Entity\Analytic\Analytic", mappedBy="context")
     * @ORM\JoinColumn(nullable=false)
     */
    private $analytics;

    /**
     * AnalyticContext constructor.
     *
     * @param string $hash       Internal analytic hash used for determining
     *                           uniqueness of analytic request.
     * @param array  $feeds      Feeds used as source for analytic.
     * @param array  $filters    Additional filters applying on data from feeds.
     * @param array  $rawFilters Filters as is it passed from frontend.
     */
    public function __construct(
        $hash,
        array $feeds,
        array $filters = [],
        array $rawFilters = []
    ) {
        if (! \app\a\allInstanceOf($feeds, AbstractFeed::class)) {
            throw new \InvalidArgumentException(sprintf(
                '\'$feeds\' should be an array of \'%s\'',
                AbstractFeed::class
            ));
        }

        if (! \app\a\allInstanceOf($filters, FilterInterface::class)) {
            throw new \InvalidArgumentException(sprintf(
                '\'$filters\' should be an array of \'%s\'',
                FilterInterface::class
            ));
        }

        $this->hash = $hash;
        $this->feeds = new ArrayCollection($feeds);
        $this->filters = $filters;
        $this->rawFilters = $rawFilters;
    }

    /**
     * Get id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash Analytic hash.
     *
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'analytic';
    }

    /**
     * @param AbstractFeed $feed A added feed.
     *
     * @return $this
     */
    public function addFeed(AbstractFeed $feed)
    {
        $this->feeds[] = $feed;

        return $this;
    }

    /**
     * @param AbstractFeed $feed A removed feed.
     *
     * @return $this
     */
    public function removeFeed(AbstractFeed $feed)
    {
        $this->feeds->removeElement($feed);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFeeds()
    {
        return $this->feeds;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param array $filters Array of normalized filters.
     *
     * @return $this
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return array
     */
    public function getRawFilters()
    {
        return $this->rawFilters;
    }

    /**
     * @param array $rawFilters Array of filters as is.
     *
     * @return $this
     */
    public function setRawFilters(array $rawFilters)
    {
        $this->rawFilters = $rawFilters;

        return $this;
    }
    /**
     * @return mixed
     */
    public function getAnalytics()
    {
        return $this->analytics;
    }

    /**
     * @param mixed $analytics
     */
    public function setAnalytics($analytics): void
    {
        $this->analytics = $analytics;
    }

    /**
     * Return metadata for current entity.
     *
     * @return Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class, [
            PropertyMetadata::createString('hash', [ 'context' ]),
            PropertyMetadata::createObject('filters', [ 'context' ]),
            PropertyMetadata::createArray('rawFilters', [ 'context' ]),
            PropertyMetadata::createArray('feeds', [ 'context' ])
                ->setField(function () {
                    $feeds = $this->feeds->map(function (AbstractFeed $feed) {
                        return [
                            'id' => $feed->getId(),
                            'name' => $feed->getName(),
                        ];
                    })->toArray();

                    return $feeds;
                }),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'context'];
    }


}
