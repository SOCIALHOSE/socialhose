<?php

namespace CacheBundle\Entity\Query;

use CacheBundle\Entity\Feed\QueryFeed;
use Common\Enum\StoredQueryStatusEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * StoredQuery
 *
 * @ORM\Entity(
 *     repositoryClass="CacheBundle\Repository\StoredQueryRepository"
 * )
 */
class StoredQuery extends AbstractQuery
{

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $limitExceed = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $lastUpdateAt;

    /**
     * @var string
     *
     * @ORM\Column
     */
    protected $status = StoredQueryStatusEnum::INITIALIZE;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="CacheBundle\Entity\Feed\QueryFeed",
     *     mappedBy="query"
     * )
     */
    protected $feeds;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->lastUpdateAt = new \DateTime();
    }

    /**
     * Set limitExceed
     *
     * @param boolean $limitExceed Flag, if true current stored query exceed
     *                             limit.
     *
     * @return StoredQuery
     */
    public function setLimitExceed($limitExceed)
    {
        $this->limitExceed = $limitExceed;

        return $this;
    }

    /**
     * Get limitExceed
     *
     * @return boolean
     */
    public function isLimitExceed()
    {
        return $this->limitExceed;
    }

    /**
     * Set lastUpdateAt
     *
     * @param \DateTime $lastUpdateAt Date of last updated of this query.
     *
     * @return StoredQuery
     */
    public function setLastUpdateAt(\DateTime $lastUpdateAt)
    {
        $this->lastUpdateAt = $lastUpdateAt;

        return $this;
    }

    /**
     * Get lastUpdateAt
     *
     * @return \DateTime
     */
    public function getLastUpdateAt()
    {
        return $this->lastUpdateAt;
    }

    /**
     * Set status
     *
     * @param string $status Stored query status.
     *
     * @return StoredQuery
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Checks that this stored query is in specified status.
     *
     * @param string|string[] $status Stored query status.
     *
     * @return boolean
     */
    public function isInStatus($status)
    {
        if (is_string($status)) {
            $status = [ $status ];
        }

        return \nspl\a\any($status, \nspl\f\partial('\nspl\op\idnt', $this->status));
    }

    /**
     * Get limitExceed
     *
     * @return boolean
     */
    public function getLimitExceed()
    {
        return $this->limitExceed;
    }

    /**
     * Add feed
     *
     * @param QueryFeed $feed A QueryFeed instance.
     *
     * @return StoredQuery
     */
    public function addFeed(QueryFeed $feed)
    {
        $this->feeds[] = $feed;
        $feed->setQuery($this);

        return $this;
    }

    /**
     * Remove feed
     *
     * @param QueryFeed $feed A QueryFeed instance.
     *
     * @return StoredQuery
     */
    public function removeFeed(QueryFeed $feed)
    {
        $this->feeds->removeElement($feed);
        $feed->setQuery(null);

        return $this;
    }

    /**
     * Get queries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeeds()
    {
        return $this->feeds;
    }
}
