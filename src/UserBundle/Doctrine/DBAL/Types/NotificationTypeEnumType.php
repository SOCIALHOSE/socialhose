<?php

namespace UserBundle\Doctrine\DBAL\Types;

use AppBundle\Doctrine\DBAL\Types\AbstractEnumType;
use UserBundle\Enum\NotificationTypeEnum;

/**
 * Class NotificationTypeEnumType
 * @package UserBundle\Doctrine\DBAL\Types
 */
class NotificationTypeEnumType extends AbstractEnumType
{

    /**
     * Return concrete enum class
     *
     * @return string
     */
    protected function getClass()
    {
        return NotificationTypeEnum::class;
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'notification_type';
    }
}
