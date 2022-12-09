<?php

namespace PaymentBundle\Gateway\Factory;

use PaymentBundle\Enum\PaymentGatewayEnum;
use PaymentBundle\Gateway\PaymentGatewayInterface;

/**
 * Interface PaymentGatewayFactoryInterface
 *
 * @package PaymentBundle\Gateway\Factory
 */
interface PaymentGatewayFactoryInterface
{

    /**
     * Get proper gateway.
     *
     * @param PaymentGatewayEnum $gateway A required payment gateway name.
     *
     * @return PaymentGatewayInterface
     */
    public function getGateway(PaymentGatewayEnum $gateway);
}
