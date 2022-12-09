<?php

namespace UserBundle\Enum;

use AppBundle\Enum\AbstractEnum;
use UserBundle\Entity\Recipient\GroupRecipient;
use UserBundle\Entity\Recipient\PersonRecipient;

/**
 * Class RecipientTypeEnum
 * @package UserBundle\Enum
 *
 * @method static RecipientTypeEnum person()
 * @method static RecipientTypeEnum group()
 */
class RecipientTypeEnum extends AbstractEnum
{

    const PERSON = 'recipient';
    const GROUP = 'group';

    private static $map = [
        self::PERSON => PersonRecipient::class,
        self::GROUP => GroupRecipient::class,
    ];

    /**
     * Get entity class for this type.
     *
     * @return string
     */
    public function getEntityClass()
    {
        return self::$map[$this->value];
    }
}
