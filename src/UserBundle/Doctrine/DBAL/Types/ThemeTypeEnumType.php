<?php

namespace UserBundle\Doctrine\DBAL\Types;

use AppBundle\Doctrine\DBAL\Types\AbstractEnumType;
use UserBundle\Enum\ThemeTypeEnum;

/**
 * Class ThemeTypeEnumType
 * @package UserBundle\Doctrine\DBAL\Types
 */
class ThemeTypeEnumType extends AbstractEnumType
{

    /**
     * Return concrete enum class
     *
     * @return string
     */
    protected function getClass()
    {
        return ThemeTypeEnum::class;
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'theme_type';
    }
}
