<?php

namespace Common\Util\Matcher\Expander;

/**
 * Class TypeExpander
 * Check that value has specified type.
 *
 * Example:
 *  - wildcart.oneOf(isEmpty(), type('double'))
 *  - wildcart.type('string')
 *
 * @package Common\Util\Matcher\Expander
 */
class TypeExpander extends AbstractExpander
{

    /**
     * @var integer
     */
    private $type;

    /**
     * @param string $type Expected value type.
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $value Value to match.
     *
     * @return boolean
     */
    public function match($value)
    {
        $valueType = gettype($value);

        if ($valueType === 'float') {
            $valueType = 'double';
        }

        return $valueType === $this->type;
    }
}
