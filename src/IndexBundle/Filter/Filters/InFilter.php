<?php

namespace IndexBundle\Filter\Filters;

use AppBundle\Enum\AbstractEnum;
use IndexBundle\Filter\AbstractFilter;
use IndexBundle\Filter\Resolver\FilterResolverInterface;
use IndexBundle\Filter\SingleFilterInterface;

/**
 * Class InFilter
 * @package IndexBundle\Filter\Filters
 */
class InFilter extends AbstractFilter implements SingleFilterInterface
{

    /**
     * @var string
     */
    protected $field;

    /**
     * @var array
     */
    protected $values;

    /**
     * @param string                                                                   $field  Filtered filed name.
     * @param string[]|integer[]|float[]|boolean[]|AbstractEnum[]|\DateTimeInterface[] $values Filter values.
     */
    public function __construct($field, array $values)
    {
        if (! is_string($field)) {
            throw new \InvalidArgumentException('\'$field\' should be string');
        }

        $this->field = trim($field);

        if (! \nspl\a\all($values, function ($value) {
            return is_scalar($value) || ($value instanceof AbstractEnum) || ($value instanceof \DateTimeInterface);
        })) {
            throw new \InvalidArgumentException('\'$values\' should be an array of scalar values, AbstractEnum or \DateTimeInterface instances');
        }

        $this->values = \nspl\a\map(function ($value) {
            if ($value instanceof AbstractEnum) {
                $value = $value->getValue();
            }

            return $value;
        }, $values);
    }

    /**
     * Resolve given filter into proper index format.
     *
     * @param FilterResolverInterface $resolver A resolver instance used for resolving
     *                                          this filter.
     *
     * @return mixed
     */
    public function resolve(FilterResolverInterface $resolver)
    {
        return $resolver->in($this->field, $this->values);
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
        return $this->values;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->field,
            $this->values,
        ]);
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
                '%s expects that unserialzied data will be an array with two items',
                static::class
            ));
        }

        $this->field = $data[0];
        $this->values = $data[1];
    }

    /**
     * @return void
     */
    public function sort()
    {
        sort($this->values);
    }
}
