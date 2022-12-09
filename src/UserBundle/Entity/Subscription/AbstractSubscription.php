<?php

namespace UserBundle\Entity\Subscription;

use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use AppBundle\Entity\OwnerAwareEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PaymentBundle\Entity\Payment;
use PaymentBundle\Enum\PaymentGatewayEnum;
use PaymentBundle\Gateway\Factory\PaymentGatewayFactoryInterface;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Plan;
use UserBundle\Entity\Traits\LimitAwareTrait;
use UserBundle\Entity\User;
use UserBundle\Enum\BillingSubscriptionTypeEnum;

/**
 * AbstractSubscription
 *
 * @ORM\Table(name="subscriptions")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\SubscriptionRepository")
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *  "organization"="OrganizationSubscription",
 *  "personal"="PersonalSubscription",
 * })
 */
abstract class AbstractSubscription implements EntityInterface
{

    use
        OwnerAwareEntityTrait,
        BaseEntityTrait,
        LimitAwareTrait;

    /**
     * @var Plan
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Plan", inversedBy="subscriptions")
     */
    private $plan;

    /**
     * @var PaymentGatewayEnum
     *
     * @ORM\Column(type="payment_gateway")
     */
    private $gateway;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\User",
     *     mappedBy="billingSubscription",
     *     cascade={ "persist", "remove" }
     * )
     */
    private $users;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\Notification\Notification",
     *     mappedBy="billingSubscription",
     *     cascade={ "persist", "remove" }
     * )
     */
    private $notifications;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="PaymentBundle\Entity\Payment",
     *     mappedBy="subscription",
     *     cascade={ "persist", "remove" }
     * )
     */
    private $payments;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $payed = false;


    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isSubscriptionCancelled = false;

     /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isPlanDowngrade = false;

    /**
     * @var datetime
     *
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $startDate;

    /**
     * @var datetime
     *
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $endDate;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    /**
     * @return Plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @param Plan $plan A Plan entity instance.
     *
     * @return $this
     */
    public function setPlan(Plan $plan = null)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * @return PaymentGatewayEnum
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param PaymentGatewayEnum $gateway A used payment gateway.
     *
     * @return static
     */
    public function setGateway(PaymentGatewayEnum $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * Add notification
     *
     * @param Notification $notification A new Notification entity instance.
     *
     * @return AbstractSubscription
     */
    public function addNotification(Notification $notification)
    {
        $this->notifications[] = $notification;
        $notification->setBillingSubscription($this);

        return $this;
    }

    /**
     * Remove notification
     *
     * @param Notification $notification A removed Notification entity instance.
     *
     * @return AbstractSubscription
     */
    public function removeNotification(Notification $notification)
    {
        $this->notifications->removeElement($notification);
        $notification->setBillingSubscription(null);

        return $this;
    }

    /**
     * Get notifications
     *
     * @return Collection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Add payment
     *
     * @param Payment $payment A new Payment entity instance.
     *
     * @return AbstractSubscription
     */
    public function addPayment(Payment $payment)
    {
        $this->payments[] = $payment;
        $payment->setSubscription($this);

        return $this;
    }

    /**
     * Remove payment
     *
     * @param Payment $payment A removed Payment entity instance.
     *
     * @return AbstractSubscription
     */
    public function removePayment(Payment $payment)
    {
        $this->payments->removeElement($payment);
        $payment->setSubscription(null);

        return $this;
    }

    /**
     * Get payments
     *
     * @return Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Add user
     *
     * @param User $user A new User entity instance.
     *
     * @return static
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;
        $user->setBillingSubscription($this);

        return $this;
    }

    /**
     * Remove user
     *
     * @param User $user A removed User entity instance.
     *
     * @return static
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
        $user->setBillingSubscription(null);

        return $this;
    }

    /**
     * Get users
     *
     * @return Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return boolean
     */
    public function isPayed()
    {
        return $this->payed;
    }

    /**
     * @param boolean $payed Has owner paid for this subscription or not.
     *
     * @return $this
     */
    public function setPayed($payed)
    {
        $this->payed = $payed;

        return $this;
    }

    /**
     * @param PaymentGatewayFactoryInterface $factory A PaymentGatewayFactoryInterface
     *                                                instance.
     * @param string                         $note    Cancel note.
     *
     * @return void
     */
    public function cancel(PaymentGatewayFactoryInterface $factory, $note)
    {
        $factory->getGateway($this->getGateway())->cancelSubscription($this, $note);
    }


      /**
     * @return bool
     */
    public function isSubscriptionCancelled(): bool
    {
        return $this->isSubscriptionCancelled;
    }

    /**
     * @param bool $isSubscriptionCancelled
     */
    public function setIsSubscriptionCancelled(bool $isSubscriptionCancelled): void
    {
        $this->isSubscriptionCancelled = $isSubscriptionCancelled;
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
     * @return startDate
     */
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * @param startDate 
     *
     * @return startDate
     */
    public function setStartDate(\DateTimeInterface $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return endDate
     */
    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * @param endDate 
     *
     * @return endDate
     */
    public function setEndDate(\DateTimeInterface $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return BillingSubscriptionTypeEnum
     */
    abstract public function getSubscriptionType();
}
