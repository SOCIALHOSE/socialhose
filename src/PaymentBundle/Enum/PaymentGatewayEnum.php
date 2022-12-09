<?php

namespace PaymentBundle\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class PaymentGatewayEnum
 *
 * @package PaymentBundle\Enum
 *
 * @method static PaymentGatewayEnum paypal()
 */
class PaymentGatewayEnum extends AbstractEnum
{

    const PAYPAL = 'paypal';
    const FREE = 'free';

    /**
     * @return array
     */
    public static function getChoices()
    {
        return [
            self::PAYPAL => 'PayPal',
            self::FREE => 'Free',
        ];
    }
}
