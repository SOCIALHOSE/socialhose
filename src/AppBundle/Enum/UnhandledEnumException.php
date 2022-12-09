<?php

namespace AppBundle\Enum;

/**
 * Class UnhandledEnumException
 *
 * @package AppBundle\Enum
 */
class UnhandledEnumException extends \RuntimeException
{

    /**
     * UnhandledEnumException constructor.
     *
     * @param string $enumClass Enumeration class.
     * @param mixed  $value     Unhandled value.
     */
    public function __construct($enumClass, $value)
    {
        parent::__construct(sprintf('Unhandled \'%s\' enum value \'%s\'', $enumClass, $value));
    }

    /**
     * @param AbstractEnum $enum A AbstractEnum instance.
     *
     * @return UnhandledEnumException
     */
    public static function fromInstance(AbstractEnum $enum)
    {
        return new self(get_class($enum), $enum->getValue());
    }
}
