<?php

namespace UserBundle\Entity;

use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use CacheBundle\Entity\Feed\AbstractFeed;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RecentlyUsedFeed
 *
 * @ORM\Table(name="recently_used_feeds")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\RecentlyUsedFeedRepository")
 */
class RecentlyUsedFeed implements EntityInterface
{

    const POOL_SIZE = 10;

    use BaseEntityTrait;

    /**
     * @var AbstractFeed
     *
     * @ORM\ManyToOne(targetEntity="CacheBundle\Entity\Feed\AbstractFeed")
     */
    private $feed;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $usedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->usedAt = new \DateTime();
    }

    /**
     * Set feed
     *
     * @param AbstractFeed $feed A AbstractFeed entity instance.
     *
     * @return RecentlyUsedFeed
     */
    public function setFeed(AbstractFeed $feed = null)
    {
        $this->feed = $feed;

        return $this;
    }

    /**
     * Get feed
     *
     * @return AbstractFeed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Set user
     *
     * @param User $user A User entity instance.
     *
     * @return RecentlyUsedFeed
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set usedAt
     *
     * @param \DateTime $usedAt A DateTime instance.
     *
     * @return RecentlyUsedFeed
     */
    public function setUsedAt(\DateTime $usedAt = null)
    {
        $this->usedAt = $usedAt;

        return $this;
    }

    /**
     * Get usedAt
     *
     * @return \DateTime
     */
    public function getUsedAt()
    {
        return $this->usedAt;
    }
}
