<?php

namespace Common\Util\Matcher\Expander;

use Coduo\ToString\StringConverter;

/**
 * Class EveryExpander
 * Check that every array elements matches specified expander.
 *
 * Example:
 *  - array.every(field('property', value))
 *  - array.every(entity('AppBundle:User', 'user, post'))
 *
 * @package Common\Util\Matcher\Expander
 */
class EveryExpander extends AbstractChainExpander
{

    /**
     * @param mixed $value Value to match.
     *
     * @return boolean
     */
    public function match($value)
    {
        if (! is_array($value)) {
            $this->error = 'Every expander require "array", got '.
                new StringConverter($value) .'.';
            return false;
        }

        foreach ($value as $row) {
            if (! parent::match($row)) {
                $this->error = 'Checked value '. new StringConverter($row)
                    .' is invalid: '. $this->error;
                return false;
            }
        }

        return true;
    }
}
