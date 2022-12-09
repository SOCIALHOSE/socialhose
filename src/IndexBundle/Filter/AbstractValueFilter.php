<?php

namespace IndexBundle\Filter;

use AppBundle\Enum\AbstractEnum;

/**
 * Class AbstractValueFilter
 * Base class for all value filters.
 *
 * @package IndexBundle\Filter
 */
abstract class AbstractValueFilter extends AbstractFilter implements
    SingleFilterInterface
{

    /**
     * @var string
     */
    protected $field;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param string                                                       $field Filtered filed name.
     * @param string|integer|float|boolean|\DateTimeInterface|AbstractEnum $value Filter value.
     */
    public function __construct($field, $value)
    {
        if (! is_string($field)) {
            throw new \InvalidArgumentException('\'$field\' should be string');
        }

        if (! is_scalar($value) && (! $value instanceof AbstractEnum) && (! $value instanceof \DateTimeInterface)) {
            throw new \InvalidArgumentException('\'$value\' should be scalar, AbstractEnum or \DateTimeInterface instance');
        }

        $this->field = trim($field);
        $this->value = $value instanceof AbstractEnum ? $value->getValue() : $value;
    }

    /**
     * Get filtered field name.
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->field;
    }

    /**
     * Get filter value.
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
    public function serialize()
    {
        return serialize([ $this->field, $this->value ]);
    }

    /**
     * @param string $serialized The string representation of the object.
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        if (! is_array($data) || (count($data) !== 2)) {
            throw new \UnexpectedValueException(sprintf(
                '%s got invalid unserialized data. Should be an array with two values',
                static::class
            ));
        }

        $this->field = $data[0];
        $this->value = $data[1];
    }

    /**
     * Sort filter values or internal filters.
     *
     * @return void
     */
    public function sort()
    {
        // do nothing.
    }
}
