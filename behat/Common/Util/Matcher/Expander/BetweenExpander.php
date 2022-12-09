<?php

namespace Common\Util\Matcher\Expander;

use Common\Util\Converter\DateConverter;

/**
 * Class BetweenExpander
 * Check that value is between specified bounds.
 * Except integers, float and datetime values.
 *
 * Example:
 *  - integer.between(10, 20)
 *  - date.between('2017-10-01', '2017-11-01')
 *  - .field('date', between('2017-10-01', '2017-11-01'))
 *
 * @package Common\Util\Matcher\Expander
 */
class BetweenExpander extends AbstractExpander
{

    /**
     * @var integer|float|string
     */
    private $start;

    /**
     * @var integer|float|string
     */
    private $end;

    /**
     * @param integer|float|string $start Start bound.
     * @param integer|float|string $end   End bound.
     */
    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
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

        $start = $this->start;
        $end = $this->end;
        if (DateConverter::can($value)) {
            // For string which represent date try to convert it into \DateTime
            // instances.
            try {
                $value = DateConverter::convert($value);
                $start = DateConverter::convert($start);
                $end = DateConverter::convert($end);

                $value->setTimezone($start->getTimezone());
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
                return false;
            }
        } else {
            // For scalar types convert all values to the same type.
            $type = gettype($start);
            settype($value, $type);
        }

        return ($value >= $start) && ($value <= $end);
    }
}
