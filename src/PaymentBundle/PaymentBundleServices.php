<?php

namespace PaymentBundle;

/**
 * Class PaymentBundleServices
 * @package PaymentBundle
 */
class PaymentBundleServices
{

    /**
     * Payment gateway.
     *
     * Must implements {@see \PaymentBundle\Gateway\Factory\PaymentGatewayFactoryInterface}
     * interface.
     */
    const PAYMENT_GATEWAY_FACTORY = 'payment.gateway_factory';

    /**
     * Agreement manager.
     *
     * Must implements {@see \PaymentBundle\Agreement\AgreementManagerInterface}
     * interface.
     */
    const AGREEMENT_MANAGER = 'payment.agreement_manager';
}
