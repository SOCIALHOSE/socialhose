<?php

namespace Common\Util\Matcher\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;

/**
 * Class FieldExpander
 * Check that inner expander not match.
 *
 * Example: .not(contains('some'))
 *
 * @package Common\Util\Matcher\Expander
 */
class NotExpander extends AbstractExpander
{
    /**
     * @var mixed
     */
    private $expander;

    /**
     * @param PatternExpander $expander A PatternExpander instance.
     */
    public function __construct(PatternExpander $expander)
    {
        $this->expander = $expander;
    }

    /**
     * @param mixed $value Value to match.
     *
     * @return boolean
     */
    public function match($value)
    {
        if ($this->expander->match($value)) {
            $this->error = 'Expander match this value.';
            return false;
        }

        return true;
    }
}
