<?php

namespace UserBundle\Entity\Subscription;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\Organization;
use UserBundle\Enum\BillingSubscriptionTypeEnum;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\SubscriptionRepository")
 */
class OrganizationSubscription extends AbstractSubscription
{

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Organization", inversedBy="subscriptions")
     */
    private $organization;

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $organizationAddress;

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $organizationEmail;

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $organizationPhone;

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param Organization $organization A Organization entity instance.
     *
     * @return OrganizationSubscription
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationAddress()
    {
        return $this->organizationAddress;
    }

    /**
     * @param string $organizationAddress Organization department address.
     *
     * @return OrganizationSubscription
     */
    public function setOrganizationAddress($organizationAddress)
    {
        $this->organizationAddress = $organizationAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationEmail()
    {
        return $this->organizationEmail;
    }

    /**
     * @param string $organizationEmail Organization department email.
     *
     * @return OrganizationSubscription
     */
    public function setOrganizationEmail($organizationEmail)
    {
        $this->organizationEmail = $organizationEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationPhone()
    {
        return $this->organizationPhone;
    }

    /**
     * @param string $organizationPhone Organization department phone.
     *
     * @return OrganizationSubscription
     */
    public function setOrganizationPhone($organizationPhone)
    {
        $this->organizationPhone = $organizationPhone;

        return $this;
    }

    /**
     * @return BillingSubscriptionTypeEnum
     */
    public function getSubscriptionType()
    {
        return BillingSubscriptionTypeEnum::organization();
    }
}
