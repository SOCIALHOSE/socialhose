<?php

namespace UserBundle\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class ThemeOptionsTableOfContentsEnum
 * @package UserBundle\Enum
 *
 * @method static ThemeOptionsTableOfContentsEnum no()
 * @method static ThemeOptionsTableOfContentsEnum simple()
 * @method static ThemeOptionsTableOfContentsEnum headline()
 * @method static ThemeOptionsTableOfContentsEnum headlineSourceDate()
 * @method static ThemeOptionsTableOfContentsEnum sourceHeadlineDate()
 */
class ThemeOptionsTableOfContentsEnum extends AbstractEnum
{

    const NO = 'no';
    const SIMPLE = 'simple';
    const HEADLINE = 'headline';
    const HEADLINE_SOURCE_DATE = 'headline_source_date';
    const SOURCE_HEADLINE_DATE = 'source_headline_date';
}
