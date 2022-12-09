<?php

namespace Common\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class SourcesAFNameEnum
 * Available advanced filters names for sources.
 *
 * @package Common\Enum
 */
class SourcesAFNameEnum extends AbstractEnum
{

    const LANG = 'language';
    const COUNTRY = 'country';
    const STATE = 'state';
    const CITY = 'city';
    const SECTION = 'section';
    const MEDIA_TYPE = 'mediaType';
}
