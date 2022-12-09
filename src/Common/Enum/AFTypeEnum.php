<?php

namespace Common\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class AFTypeEnum
 * Available advanced filters types.
 *
 * @package Common\Enum
 */
class AFTypeEnum extends AbstractEnum
{

    const QUERY = 'query';
    const SIMPLE = 'simple';
    const RANGE = 'range';
}
