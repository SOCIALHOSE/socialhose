<?php

namespace UserBundle\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class ThemeTypeEnum
 * @package UserBundle\Enum
 *
 * @method static ThemeTypeEnum enhanced()
 * @method static ThemeTypeEnum plain()
 */
class ThemeTypeEnum extends AbstractEnum
{

    const ENHANCED = 'enhanced';
    const PLAIN = 'plain';
}
