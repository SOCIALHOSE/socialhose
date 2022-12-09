<?php

namespace PaymentBundle\Gateway;

use PaymentBundle\Model\BillingSubscription;
use PaymentBundle\Model\PaymentNotification;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Plan;
use UserBundle\Entity\Subscription\AbstractSubscription;

/**
 * Interface PaymentGatewayInterface
 *
 * @package PaymentBundle\Gateway
 */
interface PaymentGatewayInterface
{

    /**
     * Update or create specific billing plan for specified application billing
     * plan.
     *
     * @param Plan $plan A application Plan entity instance.
     *
     * @return void
     */
    public function updatePlan(Plan $plan);

    /**
     * Remove specified billing plan.
     *
     * @param Plan $plan A removed application billing Plan entity instance.
     *
     * @return void
     */
    public function removePlan(Plan $plan);

    /**
     * Execute specified subscription.
     *
     * @param BillingSubscription $subscription A Subscription instance.
     *
     * @return void
     */
    public function executeSubscription(BillingSubscription $subscription);

    /**
     * Process payment notification.
     *
     * @param Request $request A HTTP Request instance.
     *
     * @return PaymentNotification
     */
    public function processNotification(Request $request);

    /**
     * Refund specified payment.
     *
     * @param AbstractSubscription $subscription A application subscription.
     * @param string               $note         A cancel note.
     *
     * @return void
     */
    public function cancelSubscription(AbstractSubscription $subscription, $note);
}
