<?php

namespace PaymentBundle\Doctrine\DBAL\Types;

use AppBundle\Doctrine\DBAL\Types\AbstractEnumType;
use PaymentBundle\Enum\PaymentStatusEnum;

/**
 * Class PaymentStatusEnumType
 *
 * @package PaymentBundle\Doctrine\DBAL\Types
 */
class PaymentStatusEnumType extends AbstractEnumType
{

    /**
     * Return concrete enum class
     *
     * @return string
     */
    protected function getClass()
    {
        return PaymentStatusEnum::class;
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'payment_status';
    }
}
