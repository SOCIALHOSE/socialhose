<?php

namespace PaymentBundle\Agreement;

use PaymentBundle\Enum\PaymentGatewayEnum;
use UserBundle\Entity\Subscription\AbstractSubscription;

/**
 * Interface AgreementManagerInterface
 *
 * @package PaymentBundle\Agreement
 */
interface AgreementManagerInterface
{

    /**
     * @param AbstractSubscription $subscription A AbstractSubscription entity
     *                                           instance.
     *
     * @return void
     */
    public function removeAgreement(AbstractSubscription $subscription);

    /**
     * @param AbstractSubscription $subscription A AbstractSubscription instance.
     *
     * @return string
     */
    public function getAgreementId(AbstractSubscription $subscription);

    /**
     * @param PaymentGatewayEnum $gateway     A used payment gateway.
     * @param string             $agreementId Gateway specific agreement id.
     *
     * @return AbstractSubscription|null
     */
    public function getSubscription(PaymentGatewayEnum $gateway, $agreementId);

    /**
     * @param AbstractSubscription $subscription User for whom we should store
     *                                           agreement.
     * @param string               $agreementId  Gateway specific agreement id.
     *
     * @return void
     */
    public function storeAgreement(AbstractSubscription $subscription, $agreementId);
}
