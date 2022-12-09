<?php

namespace PaymentBundle\Gateway\Factory;

use PaymentBundle\Enum\PaymentGatewayEnum;
use PaymentBundle\Gateway\PaymentGatewayInterface;

/**
 * Class StaticPaymentGatewayFactory
 *
 * @package PaymentBundle\Gateway\Factory
 */
class StaticPaymentGatewayFactory implements PaymentGatewayFactoryInterface
{

    /**
     * @var PaymentGatewayInterface
     */
    private $gateway;

    /**
     * StaticPaymentGatewayFactory constructor.
     *
     * @param PaymentGatewayInterface $gateway A PaymentGatewayInterface instance.
     */
    public function __construct(PaymentGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Get proper gateway.
     *
     * @param PaymentGatewayEnum $gateway A required payment gateway name.
     *
     * @return PaymentGatewayInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getGateway(PaymentGatewayEnum $gateway)
    {
        return $this->gateway;
    }
}
