<?php

namespace UserBundle\Entity;

use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\Subscription\OrganizationSubscription;

/**
 * Organization
 *
 * @ORM\Table(name="organizations")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\OrganizationRepository")
 *
 * @UniqueEntity(fields={"name"})
 */
class Organization implements EntityInterface
{

    use BaseEntityTrait;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\Subscription\OrganizationSubscription",
     *     mappedBy="organization",
     *     cascade={ "persist", "remove" }
     * )
     */
    private $subscriptions;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name A Organization name.
     *
     * @return Organization
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

    /**
     * Add subscription
     *
     * @param OrganizationSubscription $subscription A new OrganizationSubscription
     *                                               entity instance.
     *
     * @return Organization
     */
    public function addSubscription(OrganizationSubscription $subscription)
    {
        $this->subscriptions[] = $subscription;
        $subscription->setOrganization($this);

        return $this;
    }

    /**
     * Remove subscription
     *
     * @param OrganizationSubscription $subscription A removed OrganizationSubscription
     *                                               entity instance.
     *
     * @return Organization
     */
    public function removeSubscription(OrganizationSubscription $subscription)
    {
        $this->subscriptions->removeElement($subscription);
        $subscription->setOrganization(null);

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
}
