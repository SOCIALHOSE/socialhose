<?php

namespace Common\Util\Matcher\Expander;

use Coduo\ToString\StringConverter;

/**
 * Class SomeExpander
 * Check that some array elements matches specified expander.
 *
 * Example:
 *  - array.some(field('property', value))
 *  - array.some(entity('AppBundle:User'), field('id', 1))
 *
 * @package Common\Util\Matcher\Expander
 */
class SomeExpander extends AbstractChainExpander
{

    /**
     * @param mixed $value Value to match.
     *
     * @return boolean
     */
    public function match($value)
    {
        if (! is_array($value)) {
            $this->error = 'Some expander require "array", got '.
                new StringConverter($value) .'.';
            return false;
        }

        foreach ($value as $row) {
            if (parent::match($row)) {
                return true;
            }
        }


        $this->error = 'No element does not match specified expanders';
        return false;
    }
}
