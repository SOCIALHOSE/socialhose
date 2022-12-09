<?php

namespace UserBundle\Entity\Subscription;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Enum\BillingSubscriptionTypeEnum;

/**
 * @ORM\Entity
 */
class PersonalSubscription extends AbstractSubscription
{

    /**
     * @return BillingSubscriptionTypeEnum
     */
    public function getSubscriptionType()
    {
        return BillingSubscriptionTypeEnum::personal();
    }
}
