<?php

namespace PaymentBundle\Model;

use PaymentBundle\Enum\PaymentGatewayEnum;
use UserBundle\Entity\User;

/**
 * Class PaymentData
 *
 * @package PaymentBundle\Model
 */
class PaymentData
{

    /**
     * @var PaymentGatewayEnum
     */
    private $gateway;

    /**
     * @var User
     */
    private $user;

    /**
     * @var CreditCard
     */
    private $creditCard;

    /**
     * PaymentData constructor.
     *
     * @param PaymentGatewayEnum $gateway    A used payment gateway.
     * @param User               $user       A User entity instance.
     * @param CreditCard         $creditCard A CreditCard instance.
     */
    public function __construct(
        PaymentGatewayEnum $gateway,
        User $user = null,
        CreditCard $creditCard = null
    ) {
        $this->user = $user;
        $this->creditCard = $creditCard;
        $this->gateway = $gateway;
    }

    /**
     * @return PaymentGatewayEnum
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return CreditCard|null
     */
    public function getCreditCard()
    {
        return $this->creditCard;
    }
}
