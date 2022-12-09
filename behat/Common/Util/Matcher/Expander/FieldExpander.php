<?php

namespace Common\Util\Matcher\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;

/**
 * Class FieldExpander
 * Check that expanded object or array has specific field which matched
 * given matcher expander.
 *
 * Example:
 *  - .field('username', contains('admin'))
 *  - .field('user', entity('AppBundle:User', 'user'), field('id', 1))
 *
 * @package Common\Util\Matcher\Expander
 */
class FieldExpander extends AbstractExpander
{

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var mixed|PatternExpander[]
     */
    private $expander;

    /**
     * @param string                $fieldName    Field name.
     * @param mixed|PatternExpander $expander     A PatternExpander instance.
     * @param PatternExpander       $expander,... A PatternExpander's instances.
     */
    public function __construct($fieldName, $expander)
    {
        $this->fieldName = $fieldName;
        $this->expander = $expander;

        if ($expander instanceof PatternExpander) {
            $expander = func_get_args();
            $length = count($expander);
            // Process all except first argument which contains field name.
            $this->expander = [];
            for ($i = 1; $i < $length; ++$i) {
                if (!$expander[$i] instanceof PatternExpander) {
                    throw new \InvalidArgumentException('Has invalid expander.');
                }

                $this->expander[] = $expander[$i];
            }
        }
    }

    /**
     * @param mixed $value Value to match.
     *
     * @return boolean
     */
    public function match($value)
    {
        if (! is_array($value) && !isset($value[$this->fieldName])) {
            return false;
        }

        if (is_array($this->expander)) {
            // Match all expanders.
            foreach ($this->expander as $expander) {
                if (! $expander->match($value[$this->fieldName])) {
                    $this->error = "Field {$this->fieldName}: expander don't matches value. ".
                        $expander->getError();
                    return false;
                }
            }

            // All expanders successfully matches.
            return true;
        }

        if ($value[$this->fieldName] !== $this->expander) {
            $this->error = "Field {$this->fieldName}: don't equal to {$this->expander}";
            return false;
        }

        return true;
    }
}
