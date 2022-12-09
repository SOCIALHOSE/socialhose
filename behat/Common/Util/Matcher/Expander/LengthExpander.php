<?php

namespace Common\Util\Matcher\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;

/**
 * Class LengthExpander
 * Check that expanded array contains specified number of elements.
 *
 * Example: array.length(2)
 *
 * @package Common\Util\Matcher\Expander
 */
class LengthExpander extends AbstractExpander
{

    /**
     * @var integer
     */
    private $count;

    /**
     * @param PatternExpander|integer $count Expected number of elements or
     *                                       appropriate pattern expander.
     */
    public function __construct($count)
    {
        if (! $count instanceof PatternExpander) {
            $count = (int) $count;
        }
        $this->count = $count;
    }

    /**
     * @param mixed $value Value to match.
     *
     * @return boolean
     */
    public function match($value)
    {
        if (! is_array($value)) {
            return false;
        }

        if ($this->count instanceof PatternExpander) {
            return $this->count->match(count($value));
        }

        return count($value) === $this->count;
    }
}
