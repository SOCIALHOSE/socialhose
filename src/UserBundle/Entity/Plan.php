<?php

namespace UserBundle\Entity;

use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\BaseEntityTrait;
use ApiBundle\Entity\NormalizableEntityInterface;
use AppBundle\Entity\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\Subscription\AbstractSubscription;
use UserBundle\Entity\Traits\LimitAwareTrait;
use UserBundle\Enum\AppPermissionEnum;

/**
 * Plan
 *
 * @ORM\Table(name="plan")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\PlanRepository")
 */
class Plan implements EntityInterface, NormalizableEntityInterface
{

    use
        BaseEntityTrait,
        LimitAwareTrait;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     */
    private $innerName;

    /**
     * One Plan has Many Subscriptions.
     *
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\Subscription\AbstractSubscription",
     *     mappedBy="plan"
     * )
     */
    private $subscriptions;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     *
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(value=0.0)
     */
    private $price;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $analytics = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $is_default = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $news = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $blog = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $reddit = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $instagram = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $twitter = false;

     /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isPlanDowngrade = false;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="plan")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * Set name
     *
     * @param string $title A Human readable plan name.
     *
     * @return Plan
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getInnerName()
    {
        return $this->innerName;
    }

    /**
     * @param string $innerName A plan inner name used for binding with plans on
     *                          payment gateways.
     *
     * @return Plan
     */
    public function setInnerName($innerName)
    {
        $this->innerName = $innerName;

        return $this;
    }

    /**
     * Set price
     *
     * @param float $price Monthly plan price.
     *
     * @return Plan
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Checks that current plan is free.
     *
     * @return boolean
     */
    public function isFree()
    {
        return $this->price <= 0.000001;
    }

    /**
     * @return boolean
     */
    public function isAnalytics()
    {
        return $this->analytics;
    }

    /**
     * @return boolean
     */
    public function isDefault()
    {
        return $this->is_default;
    }

    /**
     * @param boolean $analytics Allow to use analytics or not.
     *
     * @return static
     */
    public function setAnalytics($analytics)
    {
        $this->analytics = $analytics;

        return $this;
    }



    /**
     * @param boolean $is_default Allow to use default or not.
     *
     * @return static
     */
    public function setIsDefault(bool $is_default)
    {
        $this->is_default = $is_default;
        return $this;
    }

    /**
     * Get specified permission.
     *
     * @param AppPermissionEnum $appPermission A requested permission name.
     *
     * @return boolean
     */
    public function getPermission(AppPermissionEnum $appPermission)
    {
        return $this->{$appPermission->getValue()};
    }

    /**
     * Change specified permission.
     *
     * @param AppPermissionEnum $appPermission A changed permission name.
     * @param boolean           $permission    New permission value.
     *
     * @return $this
     */
    public function setPermission(AppPermissionEnum $appPermission, $permission)
    {
        $this->{$appPermission->getValue()} = $permission;

        return $this;
    }

    /**
     * Add subscription
     *
     * @param AbstractSubscription $subscription A new subscription entity instance.
     *
     * @return Plan
     */
    public function addSubscription(AbstractSubscription $subscription)
    {
        $this->subscriptions[] = $subscription;
        $subscription->setPlan($this);

        return $this;
    }

    /**
     * Remove subscription
     *
     * @param AbstractSubscription $subscription A removed subscription entity
     *                                           instance.
     *
     * @return Plan
     */
    public function removeSubscription(AbstractSubscription $subscription)
    {
        $this->subscriptions->removeElement($subscription);
        $subscription->setPlan(null);

        return $this;
    }

    /**
     * Get subscriptions
     *
     * @return Collection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'plan', 'id', 'name' ];
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'plan';
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
            PropertyMetadata::createString('name', [ 'plan' ])
                ->setField('title'),
            PropertyMetadata::createInteger('searchesPerDay', [ 'plan' ]),
            PropertyMetadata::createInteger('savedFeeds', [ 'plan' ]),
            PropertyMetadata::createInteger('masterAccounts', [ 'plan' ]),
            PropertyMetadata::createInteger('subscriberAccounts', [ 'plan' ]),
            PropertyMetadata::createInteger('alerts', [ 'plan' ]),
            PropertyMetadata::createInteger('newsletters', [ 'plan' ]),
            PropertyMetadata::createBoolean('analytics', [ 'plan' ]),
            PropertyMetadata::createDouble('price', [ 'plan' ]),
            PropertyMetadata::createBoolean('free', [ 'plan' ])
                ->setField(function () {
                    return $this->isFree();
                }),
            PropertyMetadata::createBoolean('is_default', [ 'plan' ]),
            PropertyMetadata::createBoolean('news', [ 'plan' ]),
            PropertyMetadata::createBoolean('blog', [ 'plan' ]),
            PropertyMetadata::createBoolean('reddit', [ 'plan' ]),
            PropertyMetadata::createBoolean('instagram', [ 'plan' ]),
            PropertyMetadata::createBoolean('twitter', [ 'plan' ]),
        ]);
    }

        /**
     * Set news
     *
     * @return $this
     */
    public function setNews($news)
    {
        $this->news = $news;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNews()
    {
        return $this->news;
    }

    /**
     * Set blog
     *
     * @return $this
     */
    public function setBlog($blog)
    {
        $this->blog = $blog;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isBlog()
    {
        return $this->blog;
    }

     /**
     * Set reddit
     *
     * @return $this
     */
    public function setReddit($reddit)
    {
        $this->reddit = $reddit;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isReddit()
    {
        return $this->reddit;
    }

     /**
     * Set instagram
     *
     * @return $this
     */
    public function setInstagram($instagram)
    {
        $this->instagram = $instagram;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInstagram()
    {
        return $this->instagram;
    }

    /**
     * Set twitter
     *
     * @return $this
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

   /**
     * @return boolean
     */
    public function isTwitter()
    {
        return $this->twitter;
    }

    /**
     * @return bool
     */
    public function isPlanDowngrade(): bool
    {
        return $this->isPlanDowngrade;
    }

    /**
     * @param bool $isPlanDowngrade
     */
    public function setIsPlanDowngrade(bool $isPlanDowngrade): void
    {
        $this->isPlanDowngrade = $isPlanDowngrade;
    }

    /**
     * Set user
     *
     * @param User $user A User entity instance.
     *
     * @return User
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

}
