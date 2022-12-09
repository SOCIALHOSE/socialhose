<?php

namespace PaymentBundle\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class PaymentStatusEnum
 *
 * @package PaymentBundle\Enum
 *
 * @method static PaymentStatusEnum pending()
 * @method static PaymentStatusEnum success()
 * @method static PaymentStatusEnum canceled()
 * @method static PaymentStatusEnum failed()
 * @method static PaymentStatusEnum refund()
 */
class PaymentStatusEnum extends AbstractEnum
{

    const PENDING = 'pending';
    const SUCCESS = 'success';
    const CANCELED = 'canceled';
    const FAILED = 'failed';
    const REFUND = 'refund';
}
