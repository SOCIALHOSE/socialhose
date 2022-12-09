<?php

namespace Common\Util\Matcher\Matcher;

use Coduo\PHPMatcher\Matcher\Matcher;
use Coduo\PHPMatcher\Parser;
use Seld\JsonLint\JsonParser;

/**
 * Class ObjectMatcher
 * Add 'object' pattern.
 * Used by object expander's which makes all useful tests.
 *
 * @package Common\Util\Matcher\Matcher
 */
class ObjectMatcher extends Matcher
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

    /**
     * Checks if matcher can match the pattern
     *
     * @param mixed $pattern Pattern.
     *
     * @return boolean
     */
    public function canMatch($pattern)
    {
        if (! is_string($pattern)) {
            return false;
        }

        return $this->parser->hasValidSyntax($pattern)
            && $this->parser->parse($pattern)->is('object');
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
        if (parent::match($value, $pattern)) {
            return true;
        }

        // Add ability to match serialized json.
        $lint = new JsonParser();
        if (is_string($value) && ($lint->lint($value) === null)) {
            $value = json_decode($value, true);
        }

        if (! is_array($value)) {
            return false;
        }

        // Check that given value is assoc array.
        if (array_keys($value) === range(0, count($value) - 1)) {
            return false;
        }

        $typePattern = $this->parser->parse($pattern);

        // Match all expanders.
        if (!$typePattern->matchExpanders($value)) {
            $this->error = $typePattern->getError();
            return false;
        }

        return true;
    }
}
