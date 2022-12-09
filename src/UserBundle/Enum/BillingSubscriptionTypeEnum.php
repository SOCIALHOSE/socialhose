<?php

namespace UserBundle\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class BillingSubscriptionTypeEnum
 * @package UserBundle\Enum
 *
 * @method static BillingSubscriptionTypeEnum organization()
 * @method static BillingSubscriptionTypeEnum personal()
 */
class BillingSubscriptionTypeEnum extends AbstractEnum
{

    const ORGANIZATION = 'organization';
    const PERSONAL = 'personal';
}
