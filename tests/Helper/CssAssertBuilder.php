<?php

namespace Tests\Helper;

use PHPUnit\Framework\TestCase;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionFont;

/**
 * Class CssAssertBuilder
 *
 * @package Helper
 */
class CssAssertBuilder
{

    const PROPERTY_WITH_VALUE_PATTERN_TPL = '/%s[^\{]*?\{[^\}]*%s:\s*%s[^\}]*\}/i';
    const PROPERTY_PATTERN_TPL = '/%s[^\{]*?\{[^\}]*%s:[^\}]*\}/i';

    private static $searchedCharacters = [
        '.',
        '[',
        ']',
        '(',
        ')',
        '{',
        '}',
        '/',
    ];

    private static $replaceCharacters = [
        '\.',
        '\[',
        '\]',
        '\(',
        '\)',
        '\{',
        '\}',
        '\/',
    ];

    /**
     * @var string
     */
    private $selector;

    /**
     * @var boolean
     */
    private $shouldExists = true;

    /**
     * @var array
     */
    private $asserts = [];

    /**
     * CssAssertBuilder constructor.
     *
     * @param string  $selector A base css element selector.
     * @param boolean $escape   Should escape specific pattern symbols or not.
     */
    public function __construct($selector, $escape = true)
    {
        if ($escape) {
            $selector = str_replace(self::$searchedCharacters, self::$replaceCharacters, $selector);
        }

        $this->selector = $selector;
    }

    /**
     * @return CssAssertBuilder
     */
    public function shouldExists()
    {
        $this->shouldExists = true;

        return $this;
    }

    /**
     * @return CssAssertBuilder
     */
    public function shouldNotExists()
    {
        $this->shouldExists = false;

        return $this;
    }

    /**
     * @param string $selector A base css element selector.
     *
     * @return CssAssertBuilder
     */
    public static function create($selector)
    {
        return new CssAssertBuilder($selector);
    }

    /**
     * @param ThemeOptionFont $font Font which should be rendered.
     *
     * @return CssAssertBuilder
     */
    public function hasFont(ThemeOptionFont $font)
    {
        $this
            ->propertyShouldBe('font-family', $font->getFamily()->getCss())
            ->propertyShouldBe('font-size', $font->getSize());

        $style = $font->getStyle();

        if ($style->isBold()) {
            $this->propertyShouldBe('font-weight', 'bold');
        } else {
            $this->propertyShouldNotBe('font-weight', 'bold');
        }

        if ($style->isItalic()) {
            $this->propertyShouldBe('font-style', 'italic');
        } else {
            $this->propertyShouldNotBe('font-style', 'italic');
        }

        if ($style->isUnderline()) {
            $this->propertyShouldBe('text-decoration', 'underline');
        } else {
            $this->propertyShouldNotBe('text-decoration', 'underline');
        }

        return $this;
    }

    /**
     * @return static
     */
    public function hasNotAnyFonts()
    {
        return $this
            ->propertyShouldNotExists('font-family')
            ->propertyShouldNotExists('font-size')
            ->propertyShouldNotExists('font-weight')
            ->propertyShouldNotExists('font-style')
            ->propertyShouldNotExists('text-decoration');
    }

    /**
     * @param string $name    Property name.
     * @param string $message Error message.
     *
     * @return static
     */
    public function propertyShouldExists($name, $message = '')
    {
        $this->asserts[] = [
            'method' => 'assertRegExp',
            'arguments' => [
                sprintf(self::PROPERTY_PATTERN_TPL, $this->selector, $name),
                '___',
                $message,
            ],
        ];

        return $this;
    }

    /**
     * @param string $name    Property name.
     * @param string $message Error message.
     *
     * @return static
     */
    public function propertyShouldNotExists($name, $message = '')
    {
        $this->asserts[] = [
            'method' => 'assertNotRegExp',
            'arguments' => [
                sprintf(self::PROPERTY_PATTERN_TPL, $this->selector, $name),
                '___',
                $message,
            ],
        ];

        return $this;
    }

    /**
     * @param string $name    Property name.
     * @param string $value   Expected property value.
     * @param string $message Error message.
     *
     * @return static
     */
    public function propertyShouldBe($name, $value, $message = '')
    {
        $name = str_replace(self::$searchedCharacters, self::$replaceCharacters, $name);
        $value = str_replace(self::$searchedCharacters, self::$replaceCharacters, $value);

        $this->asserts[] = [
            'method' => 'assertRegExp',
            'arguments' => [
                sprintf(self::PROPERTY_WITH_VALUE_PATTERN_TPL, $this->selector, $name, $value),
                '___',
                $message,
            ],
        ];

        return $this;
    }

    /**
     * @param string $name    Property name.
     * @param string $value   Expected property value.
     * @param string $message Error message.
     *
     * @return static
     */
    public function propertyShouldNotBe($name, $value = '', $message = '')
    {
        $name = str_replace(self::$searchedCharacters, self::$replaceCharacters, $name);
        $value = str_replace(self::$searchedCharacters, self::$replaceCharacters, $value);

        $this->asserts[] = [
            'method' => 'assertNotRegExp',
            'arguments' => [
                sprintf(self::PROPERTY_WITH_VALUE_PATTERN_TPL, $this->selector, $name, $value),
                '___',
                $message,
            ],
        ];

        return $this;
    }

    /**
     * @param string $html HTML content.
     *
     * @return void
     */
    public function assert($html)
    {
        if (! $this->shouldExists) {
            TestCase::assertNotRegExp("/{$this->selector}/", $html);

            // Don't assert property 'cause it's not necessary.
            return;
        }

        foreach ($this->asserts as $config) {
            $arguments = \nspl\a\map(function ($argument) use ($html) {
                if (is_string($argument) && ($argument === '___')) {
                    $argument = $html;
                }

                return $argument;
            }, $config['arguments']);
            call_user_func_array([ TestCase::class, $config['method'] ], $arguments);
        }
    }
}
