<?php

namespace Tests\Helper;

use PHPUnit\Framework\TestCase;

/**
 * Class CssAsserter
 *
 * @package Helper
 */
class CssAsserter
{

    /**
     * @var string
     */
    private $styles;

    /**
     * CssAsserter constructor.
     *
     * @param string $styles Raw css style text.
     */
    public function __construct($styles)
    {
        $this->styles = $styles;
    }

    /**
     * @param string $html Raw html.
     *
     * @return CssAsserter
     */
    public static function createFromHtml($html)
    {
        $matches = [];
        preg_match('#<style>([^<]*)</style>#i', $html, $matches);
        array_shift($matches);

        return new CssAsserter(implode("\n", $matches));
    }

    /**
     * @param string $selector Css selector.
     *
     * @return CssAsserter
     */
    public function hasSelector($selector)
    {
        TestCase::assertRegExp('/'.$selector.'/i', $this->styles);

        return $this;
    }

    /**
     * @param string $selector Css selector.
     *
     * @return CssPropertiesAsserter
     */
    public function with($selector)
    {
        $this->hasSelector($selector);

        return new CssPropertiesAsserter(preg_replace('/%s[^\{]*?\{([^\}]*?)\}/i', '$1', $this->styles), $this);
    }
}
