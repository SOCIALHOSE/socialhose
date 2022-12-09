<?php

namespace Common\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class CollectionTypeEnum
 * @package Common\Enum
 *
 * @method static CollectionTypeEnum query()
 * @method static CollectionTypeEnum feed()
 */
class CollectionTypeEnum extends AbstractEnum
{

    const QUERY = 'query';
    const FEED = 'feed';
}
