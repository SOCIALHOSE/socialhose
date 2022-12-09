<?php

namespace PaymentBundle\Doctrine\DBAL\Types;

use AppBundle\Doctrine\DBAL\Types\AbstractEnumType;
use PaymentBundle\Enum\PaymentGatewayEnum;

/**
 * Class PaymentGatewayEnumType
 *
 * @package PaymentBundle\Doctrine\DBAL\Types
 */
class PaymentGatewayEnumType extends AbstractEnumType
{

    /**
     * Return concrete enum class
     *
     * @return string
     */
    protected function getClass()
    {
        return PaymentGatewayEnum::class;
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'payment_gateway';
    }
}
