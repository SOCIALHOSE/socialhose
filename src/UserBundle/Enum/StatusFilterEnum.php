<?php

namespace UserBundle\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class ThemeTypeEnum
 * @package UserBundle\Enum
 *
 * @method static StatusFilterEnum no()
 * @method static StatusFilterEnum yes()
 * @method static StatusFilterEnum all()
 */
class StatusFilterEnum extends AbstractEnum
{

    const NO = 'no';
    const YES = 'yes';
    const ALL = 'all';
}
