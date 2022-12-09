<?php

namespace AppBundle\Doctrine\DBAL\Types;

use AppBundle\Enum\AbstractEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Class AbstractEnumType
 * @package AppBundle\Doctrine\DBAL\Types
 */
abstract class AbstractEnumType extends Type
{

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
        if (is_string($value)) {
            $class = $this->getClass();
            $value = new $class($value);
        }

        if (! $value instanceof AbstractEnum) {
            throw new \InvalidArgumentException(
                'Invalid value, must be instance of '. AbstractEnum::class
                .'  but '. (is_object($value) ? get_class($value) : gettype($value))
                .' given'
            );
        }

        return $value->getValue();
    }

    /**
     * Return concrete enum class
     *
     * @return string
     */
    abstract protected function getClass();

    /**
     * Converts a value from its database representation to its PHP
     * representation of this type.
     *
     * @param mixed            $value    The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The PHP representation of the value.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $class = $this->getClass();

        if ($value !== null) {
            $value = new $class($value);
        }

        return $value;
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
}
