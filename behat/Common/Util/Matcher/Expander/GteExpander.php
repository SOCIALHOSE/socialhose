<?php

namespace Common\Util\Matcher\Expander;

use Common\Util\Converter\DateConverter;

/**
 * Class GteExpander
 * Check that value is greater or equal to another value.
 * Except integers, float and datetime values.
 *
 * Example:
 *  - integer.gte(10)
 *  - date.gte('2017-10-01')
 *  - .field('date', gte('2017-10-01'))
 *
 * @package Common\Util\Matcher\Expander
 */
class GteExpander extends AbstractExpander
{

    /**
     * @var integer|string|\DateTimeInterface|float
     */
    private $value;

    /**
     * @param integer|string|\DateTimeInterface|float $value Expected value.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param mixed $value Value to match.
     *
     * @return boolean
     */
    public function match($value)
    {
        if (! is_numeric($value) && ! is_int($value) && ! is_float($value)
            && ! is_string($value)) {
            $this->error = 'Can match only integers, float and datetime values';
            return false;
        }

        $bound = $this->value;
        if (DateConverter::can($value)) {
            // For string which represent date try to convert it into \DateTime
            // instances.
            try {
                $value = DateConverter::convert($value);
                $bound = DateConverter::convert($bound);

                $bound->setTimezone($value->getTimezone());
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
                return false;
            }
        } else {
            // For scalar types convert all values to the same type.
            $type = gettype($bound);
            settype($value, $type);
        }

        if (! ($matched = $value >= $bound)) {
            if ($value instanceof \DateTime) {
                $value = $value->format('c');
                $bound = $bound->format('c');
            }

            $this->error = "Checked value {$value} less than {$bound}.";
        }

        return $matched;
    }
}
