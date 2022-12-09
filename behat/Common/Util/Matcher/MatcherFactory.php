<?php

namespace Common\Util\Matcher;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\ChainMatcher;
use Coduo\PHPMatcher\Parser;
use Coduo\PHPMatcher\Matcher;
use Common\Util\Matcher\Expander as AppExpanders;
use Common\Util\Matcher\Matcher as AppMatchers;

/**
 * Class MatcherFactory
 * @package Common\Util\Matcher
 */
class MatcherFactory extends SimpleFactory
{

    /**
     * @var Parser
     */
    private static $parser;

    /**
     * @var array
     */
    private static $additionalExpanders = [
        'field' => AppExpanders\FieldExpander::class,
        'some' => AppExpanders\SomeExpander::class,
        'every' => AppExpanders\EveryExpander::class,
        'length' => AppExpanders\LengthExpander::class,
        'one' => AppExpanders\OneExpander::class,
        'type' => AppExpanders\TypeExpander::class,
        'entity' => AppExpanders\EntityExpander::class,
        'not' => AppExpanders\NotExpander::class,
        'gte' => AppExpanders\GteExpander::class,
        'between' => AppExpanders\BetweenExpander::class,
    ];

    /**
     * @return ChainMatcher
     */
    protected function buildScalarMatchers()
    {
        $parser = $this->buildParser();

        return new Matcher\ChainMatcher([
            // Default matchers.
            new Matcher\CallbackMatcher(),
            new Matcher\ExpressionMatcher(),
            new Matcher\NullMatcher(),
            new Matcher\StringMatcher($parser),
            new Matcher\IntegerMatcher($parser),
            new Matcher\BooleanMatcher(),
            new Matcher\DoubleMatcher($parser),
            new Matcher\NumberMatcher(),
            new Matcher\ScalarMatcher(),

            // Custom matchers.
            new AppMatchers\ObjectMatcher($parser),
            new AppMatchers\WildcardMatcher($parser),
        ]);
    }

    /**
     * @return Parser
     */
    protected function buildParser()
    {
        if (!self::$parser) {
            // Register all expanders.
            $expanderInitializer = new Parser\ExpanderInitializer();

            foreach (self::$additionalExpanders as $name => $class) {
                $expanderInitializer->setExpanderDefinition($name, $class);
            }

            self::$parser = new Parser(new Lexer(), $expanderInitializer);
        }

        return self::$parser;
    }

    /**
     * @return \Coduo\PHPMatcher\Matcher\ChainMatcher
     */
    protected function buildMatchers()
    {
        $scalarMatchers = $this->buildScalarMatchers();
        $orMatcher = $this->buildOrMatcher();

        $chainMatcher = new Matcher\ChainMatcher([
            $scalarMatchers,
            $orMatcher,
            new AppMatchers\JsonMatcher($orMatcher),
            new Matcher\XmlMatcher($orMatcher),
            new Matcher\TextMatcher($scalarMatchers, $this->buildParser()),
        ]);

        return $chainMatcher;
    }
}
