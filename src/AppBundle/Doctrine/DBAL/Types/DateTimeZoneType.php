<?php

namespace AppBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

/**
 * Class DateTimeZoneType
 * @package AppBundle\Doctrine\DBAL\Types
 */
class DateTimeZoneType extends Type
{
    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'datetimezone';
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array            $fieldDeclaration The field declaration.
     * @param AbstractPlatform $platform         The currently used database
     *                                           platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * Gets the default length of this type.
     *
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return integer|null
     */
    public function getDefaultLength(AbstractPlatform $platform)
    {
        return $platform->getVarcharDefaultLength();
    }

    /**
     * Converts a value from its PHP representation to its database
     * representation of this type.
     *
     * @param mixed            $value    The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The database representation of the value.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (! $value instanceof \DateTimeZone) {
            throw new \InvalidArgumentException(
                'Expects \DateTimeZone, but got '. gettype($value)
            );
        }

        return $value->getName();
    }

    /**
     * Converts a value from its database representation to its PHP
     * representation of this type.
     *
     * @param mixed            $value    The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The PHP representation of the value.
     *
     * @throws ConversionException If can't convert from database to php value.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $val = new \DateTimeZone($value);

        if (! $val instanceof \DateTimeZone) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }
}
