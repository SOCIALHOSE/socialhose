<?php

namespace UserBundle\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class ThemeOptionsUserCommentsEnum
 * @package UserBundle\Enum
 *
 * @method static ThemeOptionsUserCommentsEnum no()
 * @method static ThemeOptionsUserCommentsEnum withAuthorDate()
 * @method static ThemeOptionsUserCommentsEnum withoutAuthorDate()
 */
class ThemeOptionsUserCommentsEnum extends AbstractEnum
{

    const NO = 'no';
    const WITH_AUTHOR_DATE = 'with_author_date';
    const WITHOUT_AUTHOR_DATE = 'without_author_date';
}
