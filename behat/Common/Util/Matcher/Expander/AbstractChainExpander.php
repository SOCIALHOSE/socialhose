<?php

namespace Common\Util\Matcher\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;

/**
 * Class AbstractChainExpander
 * @package Common\Util\Matcher\Expander
 */
abstract class AbstractChainExpander extends AbstractExpander
{

    /**
     * @var PatternExpander[]
     */
    protected $expanders = [];

    /**
     * @param PatternExpander $expander     A PatternExpander instance.
     * @param PatternExpander $expander,... A PatternExpander's instances.
     */
    public function __construct(PatternExpander $expander)
    {
        $this->expanders[] = $expander;

        if (func_num_args() > 1) {
            $arguments = func_get_args();
            $length = count($arguments);
            for ($i = 1; $i < $length; ++$i) {
                if (!$arguments[$i] instanceof PatternExpander) {
                    throw new \InvalidArgumentException('Has invalid expander.');
                }

                $this->expanders[] = $arguments[$i];
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
        foreach ($this->expanders as $expander) {
            if (! $expander->match($value)) {
                $className = get_class($expander);
                $className = substr($className, strrpos($className, '\\'));

                $this->error = "Expander {$className} don't matches value: ".
                    $expander->getError();
                return false;
            }
        }

        return true;
    }
}
