<?php

namespace Common\Util\Matcher\Expander;

use Coduo\ToString\StringConverter;

/**
 * Class OneExpander
 * Check that only one array element matches specified expander.
 *
 * Example:
 *  - array.one(field('property', value))
 *  - array.one(entity('AppBundle:User'), field('id', 1))
 *
 * @package Common\Util\Matcher\Expander
 */
class OneExpander extends AbstractChainExpander
{
    /**
     * @param mixed $value Value to match.
     *
     * @return boolean
     */
    public function match($value)
    {
        if (! is_array($value)) {
            $this->error = 'One expander require "array", got '.
                new StringConverter($value) .'.';
            return false;
        }

        foreach ($value as $row) {
            if (parent::match($row)) {
                return true;
            }
        }

        return false;
    }
}
