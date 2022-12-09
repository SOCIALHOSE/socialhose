<?php

namespace UserBundle\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class NotificationTypeEnum
 * @package UserBundle\Enum
 *
 * @method static NotificationTypeEnum alert()
 * @method static NotificationTypeEnum newsletter()
 */
class NotificationTypeEnum extends AbstractEnum
{

    const ALERT = 'alert';
    const NEWSLETTER = 'newsletter';

    /**
     * @return AppLimitEnum
     */
    public function toAppLimit()
    {
        if ($this->value === self::ALERT) {
            return AppLimitEnum::alerts();
        }

        return AppLimitEnum::newsletters();
    }
}
