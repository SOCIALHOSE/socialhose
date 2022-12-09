<?php

namespace UserBundle\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionColors;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionColorsBackground;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionColorsText;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionContent;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionFont;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionFonts;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionHeader;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionHighlightKeywords;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionShowInfo;
use UserBundle\Enum\FontFamilyEnum;
use UserBundle\Enum\ThemeOptionExtractEnum;
use UserBundle\Enum\ThemeOptionsTableOfContentsEnum;
use UserBundle\Enum\ThemeOptionsUserCommentsEnum;

/**
 * NotificationThemeOptions
 *
 * @ORM\Embeddable
 */
class NotificationThemeOptions
{

    const DEFAULT_HEADER_SIZE = 18;
    const DEFAULT_TABLE_OF_CONTENTS_SIZE = 12;
    const DEFAULT_FEED_TITLE_SIZE = 12;
    const DEFAULT_ARTICLE_HEADLINE_SIZE = 16;
    const DEFAULT_SOURCE_SIZE = 12;
    const DEFAULT_AUTHOR_SIZE = 12;
    const DEFAULT_DATE_SIZE = 12;
    const DEFAULT_ARTICLE_CONTENT_SIZE = 12;

    /**
     * Text which locate just after header but before content.
     * Contains HTML.
     *
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $summary;

    /**
     * Text which locate before footer just after content.
     * Contains HTML.
     *
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $conclusion;

    /**
     * @var ThemeOptionHeader
     *
     * @ORM\Column(type="object")
     */
    private $header;

    /**
     * @var ThemeOptionFonts
     *
     * @ORM\Column(type="object")
     */
    private $fonts;

    /**
     * @var ThemeOptionContent
     *
     * @ORM\Column(type="object")
     */
    private $content;

    /**
     * @var ThemeOptionColors
     *
     * @ORM\Column(type="object")
     */
    private $colors;

    /**
     * NotificationThemeOptions constructor.
     *
     * @param string             $summary    Summary text.
     * @param string             $conclusion Conclusion text.
     * @param ThemeOptionHeader  $header     A ThemeOptionHeader instance.
     * @param ThemeOptionFonts   $fonts      A ThemeOptionFonts instance.
     * @param ThemeOptionContent $content    A ThemeOptionContent instance.
     * @param ThemeOptionColors  $colors     A ThemeOptionColors instance.
     */
    public function __construct(
        $summary,
        $conclusion,
        ThemeOptionHeader $header,
        ThemeOptionFonts $fonts,
        ThemeOptionContent $content,
        ThemeOptionColors $colors
    ) {
        $this->summary = trim($summary);
        $this->conclusion = trim($conclusion);
        $this->header = $header;
        $this->fonts = $fonts;
        $this->content = $content;
        $this->colors = $colors;
    }

    /**
     * @return static
     */
    public static function createDefault()
    {
        return new static(
            '',
            '',
            new ThemeOptionHeader(
                ThemeOptionHeader::DEFAULT_IMAGE,
                '',
                'Newsletter'
            ),
            new ThemeOptionFonts(
                new ThemeOptionFont(FontFamilyEnum::arial(), self::DEFAULT_HEADER_SIZE),
                new ThemeOptionFont(FontFamilyEnum::arial(), self::DEFAULT_TABLE_OF_CONTENTS_SIZE),
                new ThemeOptionFont(FontFamilyEnum::arial(), self::DEFAULT_FEED_TITLE_SIZE),
                new ThemeOptionFont(FontFamilyEnum::arial(), self::DEFAULT_ARTICLE_HEADLINE_SIZE),
                new ThemeOptionFont(FontFamilyEnum::arial(), self::DEFAULT_SOURCE_SIZE),
                new ThemeOptionFont(FontFamilyEnum::arial(), self::DEFAULT_AUTHOR_SIZE),
                new ThemeOptionFont(FontFamilyEnum::arial(), self::DEFAULT_DATE_SIZE),
                new ThemeOptionFont(FontFamilyEnum::arial(), self::DEFAULT_ARTICLE_CONTENT_SIZE)
            ),
            new ThemeOptionContent(
                new ThemeOptionHighlightKeywords(),
                new ThemeOptionShowInfo(
                    ThemeOptionsUserCommentsEnum::no(),
                    ThemeOptionsTableOfContentsEnum::simple()
                ),
                'en',
                ThemeOptionExtractEnum::context()
            ),
            new ThemeOptionColors(
                new ThemeOptionColorsBackground(),
                new ThemeOptionColorsText()
            )
        );
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return boolean
     */
    public function hasSummary()
    {
        return $this->summary !== '';
    }

    /**
     * @param string $summary Summary text.
     *
     * @return NotificationThemeOptions
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return string
     */
    public function getConclusion()
    {
        return $this->conclusion;
    }

    /**
     * @return boolean
     */
    public function hasConclusion()
    {
        return $this->conclusion !== '';
    }

    /**
     * @param string $conclusion Conclusion text.
     *
     * @return NotificationThemeOptions
     */
    public function setConclusion($conclusion)
    {
        $this->conclusion = $conclusion;

        return $this;
    }

    /**
     * @return ThemeOptionHeader
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param ThemeOptionHeader $header A ThemeOptionHeader instance.
     *
     * @return NotificationThemeOptions
     */
    public function setHeader(ThemeOptionHeader $header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return ThemeOptionFonts
     */
    public function getFonts()
    {
        return $this->fonts;
    }

    /**
     * @param ThemeOptionFonts $fonts A ThemeOptionFonts instance.
     *
     * @return NotificationThemeOptions
     */
    public function setFonts(ThemeOptionFonts $fonts)
    {
        $this->fonts = $fonts;

        return $this;
    }

    /**
     * @return ThemeOptionContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param ThemeOptionContent $content A ThemeOptionContent instance.
     *
     * @return NotificationThemeOptions
     */
    public function setContent(ThemeOptionContent $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return ThemeOptionColors
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * @param ThemeOptionColors $colors A ThemeOptionColors instace.
     *
     * @return NotificationThemeOptions
     */
    public function setColors(ThemeOptionColors $colors)
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'summary' => $this->summary,
            'conclusion' => $this->conclusion,
            'header' => $this->header->toArray(),
            'fonts' => $this->fonts->toArray(),
            'content' => $this->content->toArray(),
            'colors' => $this->colors->toArray(),
        ];
    }
}
