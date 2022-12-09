<?php

namespace PaymentBundle\Model;

use UserBundle\Entity\Plan;
use UserBundle\Entity\Subscription\AbstractSubscription;

/**
 * Class BillingSubscription
 *
 * @package PaymentBundle\Model
 */
class BillingSubscription
{

    /**
     * @var AbstractSubscription
     */
    private $subscription;

    /**
     * @var Plan
     */
    private $plan;

    /**
     * @var CreditCard|null
     */
    private $creditCard;

    /**
     * BillingSubscription constructor.
     *
     * @param AbstractSubscription $subscription A application subscription entity
     *                                           instance.
     * @param Plan                 $plan         A plan entity on which user
     *                                           wants to subscribe.
     * @param CreditCard           $creditCard   User credit card.
     */
    public function __construct(
        AbstractSubscription $subscription,
        Plan $plan,
        CreditCard $creditCard = null
    ) {
        $this->subscription = $subscription;
        $this->plan = $plan;
        $this->creditCard = $creditCard;
    }

    /**
     * @return AbstractSubscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return Plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @return CreditCard|null
     */
    public function getCreditCard()
    {
        return $this->creditCard;
    }
}
