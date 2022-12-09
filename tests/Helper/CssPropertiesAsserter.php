<?php

namespace Tests\Helper;

use PHPUnit\Framework\TestCase;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionFont;

/**
 * Class CssPropertiesAsserter
 *
 * @package Helper
 */
class CssPropertiesAsserter
{

    /**
     * @var string
     */
    private $properties;

    /**
     * @var CssAsserter
     */
    private $cssAsserter;

    /**
     * CssAsserter constructor.
     *
     * @param string           $properties  Css properties.
     * @param CssAsserter|null $cssAsserter A parent CssAsserter instance.
     */
    public function __construct($properties, CssAsserter $cssAsserter)
    {
        $this->properties = $properties;
        $this->cssAsserter = $cssAsserter;
    }

    /**
     * @param string      $name  Property name.
     * @param string|null $value Property value.
     *
     * @return CssPropertiesAsserter
     */
    public function has($name, $value = null)
    {
        if ($value !== null) {
            $value = str_replace([ '(', ')' ], [ '\(', '\)' ], $value);
        }

        $pattern = $value === null
            ? sprintf('/%s:\s*[^;];/i', $name)
            : sprintf('/%s:\s*%s;/i', $name, $value);

        TestCase::assertRegExp($pattern, $this->properties);

        return $this;
    }

    /**
     * @param string      $name  Property name.
     * @param string|null $value Property value.
     *
     * @return CssPropertiesAsserter
     */
    public function hasNot($name, $value = null)
    {
        $pattern = $value === null
            ? sprintf('/%s:\s*[^;];/i', $name)
            : sprintf('/%s:\s*%s;/i', $name, $value);

        TestCase::assertNotRegExp($pattern, $this->properties);

        return $this;
    }

    /**
     * @param ThemeOptionFont $font A ThemeOptionFont instance.
     *
     * @return CssPropertiesAsserter
     */
    public function hasFont(ThemeOptionFont $font)
    {
        $this
            ->has('font-family', $font->getFamily()->getCss())
            ->has('font-size', $font->getSize());

        $style = $font->getStyle();

        if ($style->isBold()) {
            $this->has('font-weight', 'bold');
        } else {
            $this->hasNot('font-weight');
        }

        if ($style->isItalic()) {
            $this->has('font-style', 'italic');
        } else {
            $this->hasNot('font-style');
        }

        if ($style->isUnderline()) {
            $this->has('text-decoration', 'underline');
        } else {
            $this->hasNot('text-decoration');
        }

        return $this;
    }

    /**
     * @return CssPropertiesAsserter
     */
    public function hasNotFontAtAll()
    {
        return $this
            ->hasNot('font-family')
            ->hasNot('font-size')
            ->hasNot('font-weight')
            ->hasNot('font-style')
            ->hasNot('text-decoration');
    }

    /**
     * @return null|CssAsserter
     */
    public function end()
    {
        return $this->cssAsserter;
    }
}
