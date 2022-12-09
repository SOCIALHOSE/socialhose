<?php

namespace Common\Util\Matcher\Matcher;

use Coduo\PHPMatcher\Matcher\Matcher;
use Coduo\PHPMatcher\Parser;

/**
 * Class WildcardMatcher
 * Replace default coduo/php-matcher WildcardMatcher in order to process
 * expanders.
 *
 * @package Common\Util\Matcher\Matcher
 */
class WildcardMatcher extends Matcher
{

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @param Parser $parser A Parser instance.
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    const MATCH_PATTERN = "@wildcard@";

    /**
     * Checks if matcher can match the pattern
     *
     * @param mixed $pattern Pattern.
     *
     * @return boolean
     */
    public function canMatch($pattern)
    {
        return is_string($pattern)
            && strpos($pattern, self::MATCH_PATTERN) !== false;
    }

    /**
     * Matches value against the pattern
     *
     * @param mixed $value   Checked value.
     * @param mixed $pattern Pattern.
     *
     * @return boolean
     */
    public function match($value, $pattern)
    {
        $typePattern = $this->parser->parse($pattern);

        // Match all expanders.
        if (!$typePattern->matchExpanders($value)) {
            $this->error = $typePattern->getError();
            return false;
        }

        return true;
    }
}
