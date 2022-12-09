<?php

namespace Common\Util\Matcher\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;

/**
 * Class AbstractExpander
 * @package Common\Util\Matcher\Expander
 */
abstract class AbstractExpander implements PatternExpander
{

    /**
     * @var string|null
     */
    protected $error;

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }
}
