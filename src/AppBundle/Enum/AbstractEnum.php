<?php

namespace AppBundle\Enum;

/**
 * Class AbstractEnum
 * @package AppBundle\Enum
 */
abstract class AbstractEnum
{

    /**
     * Cached constants.
     *
     * @var array
     */
    private static $cache = [];

    /**
     * @var mixed
     */
    protected $value;

    /**
     * AbstractEnum constructor.
     *
     * @param mixed $value One of availables enum values.
     */
    public function __construct($value)
    {
        if (! self::isValid($value)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown value %s for enum %s. Expects one of %s',
                $value,
                static::class,
                implode(', ', self::getAvailables())
            ));
        }

        $this->value = $value;
    }

    /**
     * Get all available enum values.
     *
     * @return AbstractEnum[]
     */
    public static function getValues()
    {
        $values = [];

        foreach (static::getAvailables() as $available) {
            //
            // Code sniffer says that: 'Use parentheses when instantiating classes'
            // but, obviously, we did it.
            //
            // @codingStandardsIgnoreStart
            $values[] = new static($available);
            // @codingStandardsIgnoreEnd
        }

        return $values;
    }

    /**
     * Get available constants values.
     *
     * @return string[]
     */
    public static function getAvailables()
    {
        $class = static::class;

        if (! isset(self::$cache[$class])) {
            $reflection = new \ReflectionClass($class);
            self::$cache[$class] = $reflection->getConstants();
        }

        return self::$cache[$class];
    }

    /**
     * Checks that specified value is valid for current enum.
     *
     * @param mixed $value Maybe one of enum value.
     *
     * @return boolean
     */
    public static function isValid($value)
    {
        return in_array($value, self::getAvailables(), true);
    }

    /**
     * @param string $name      Method name.
     * @param mixed  $arguments Method arguments.
     *
     * @return static
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function __callStatic($name, $arguments)
    {
        // camelCase to underscore.
        $name = strtoupper(preg_replace('/([^A-Z-])([A-Z])/', '$1_$2', $name));
        $availables = self::getAvailables();

        if (array_key_exists($name, $availables)) {
            //
            // Code sniffer says that: 'Use parentheses when instantiating classes'
            // but, obviously, we did it.
            //
            // @codingStandardsIgnoreStart
            return new static($availables[$name]);
            // @codingStandardsIgnoreEnd
        }

        throw new \RuntimeException("Unknown enum value '{$name}'");
    }

    /**
     * Checks that current value is equal to specified.
     *
     * @param AbstractEnum|string $enum One of availables enum values.
     *
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function is($enum)
    {
        if (is_scalar($enum)) {
            //
            // Code sniffer says that: 'Use parentheses when instantiating classes'
            // but, obviously, we did it.
            //
            // @codingStandardsIgnoreStart
            $enum = new static($enum);
            // @codingStandardsIgnoreEnd
        }

        if (! $enum instanceof static) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown value %s for enum %s. Expects one of %s',
                $enum,
                static::class,
                implode(', ', self::getAvailables())
            ));
        }

        return $this->value === $enum->getValue();
    }

    /**
     * Get value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}
