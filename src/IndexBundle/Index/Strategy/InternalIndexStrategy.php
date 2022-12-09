<?php

namespace IndexBundle\Index\Strategy;

use Common\Enum\FieldNameEnum;

/**
 * Class InternalIndexStrategy
 *
 * @package IndexBundle\Index\Strategy
 */
class InternalIndexStrategy extends HoseIndexStrategy // Because we have only hose
{

    /**
     * Name of the fields which have 'raw' fields.
     *
     * @var array
     */
    public static $rawFieldNameMap = [
        FieldNameEnum::SOURCE_TITLE,
        FieldNameEnum::SECTION,
        FieldNameEnum::AUTHOR_NAME,
        FieldNameEnum::PUBLISHER,
        FieldNameEnum::COUNTRY,
        FieldNameEnum::STATE,
        FieldNameEnum::CITY,
    ];
}
