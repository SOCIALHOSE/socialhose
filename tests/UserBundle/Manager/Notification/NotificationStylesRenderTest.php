<?php

namespace UserBundle\Manager\Notification;

use Tests\Helper\CssAssertBuilder;
use Tests\Helper\CssAsserter;
use UserBundle\Entity\Notification\NotificationThemeOptions;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionColorsBackground;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionColorsText;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionFont;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionFontStyle;
use UserBundle\Enum\FontFamilyEnum;
use UserBundle\Enum\ThemeTypeEnum;
use UserBundle\Manager\Notification\Model\FeedData;

/**
 * Class NotificationStylesRenderTest
 *
 * @package UserBundle\Manager\Notification
 */
class NotificationStylesRenderTest extends AbstractSendableNotificationTest
{

    /**
     * @return void
     */
    public function testLayout()
    {
        $defaultEmailBodyBG = ThemeOptionColorsBackground::DEFAULT_EMAIL_BODY;
        $defaultAccentBG = ThemeOptionColorsBackground::DEFAULT_ACCENT;
        $defaultArticleContentFG = ThemeOptionColorsText::DEFAULT_ARTICLE_CONTENT;

        $customEmailBodyBG = 'rgba(123, 44, 55, 0.32)';
        $customAccentBG = 'rgba(124, 45, 56, 0.33)';
        $customArticleContentFG = 'rgba(125, 46, 57, 0.34)';

        $this->createAsserter(ThemeTypeEnum::plain())
            ->with('.email')->hasNot('border')->end()
            ->with('html')->hasNot('background')->end()
            ->with('body')->hasNot('background')->end()
            ->with('.email-body-content')->has('color', $defaultArticleContentFG)->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'colors:background:emailBody' => $customEmailBodyBG,
            'colors:background:accent'    => $customAccentBG,
            'colors:text:articleContent'  => $customArticleContentFG,
        ])
            ->with('.email')->hasNot('border')->end()
            ->with('html')->hasNot('background')->end()
            ->with('body')->hasNot('background')->end()
            ->with('.email-body-content')->has('color', $customArticleContentFG)->end();

        $this->createAsserter(ThemeTypeEnum::enhanced())
            ->with('.email')->has('border', '4px solid '. $defaultAccentBG)->end()
            ->with('html')->has('background', $defaultEmailBodyBG)->end()
            ->with('body')->has('background', $defaultEmailBodyBG)->end()
            ->with('.email-body-content')->has('color', $defaultArticleContentFG)->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'colors:background:emailBody' => $customEmailBodyBG,
            'colors:background:accent'    => $customAccentBG,
            'colors:text:articleContent'  => $customArticleContentFG,
        ])
            ->with('.email')->has('border', '4px solid '. $customAccentBG)->end()
            ->with('html')->has('background', $customEmailBodyBG)->end()
            ->with('body')->has('background', $customEmailBodyBG)->end()
            ->with('.email-body-content')->has('color', $customArticleContentFG)->end();
    }

    /**
     * @return void
     */
    public function testHeader()
    {
        $this->assertCssRender(ThemeTypeEnum::plain(), [
            $this->createCssAssertBuilder('.email-header')
                ->propertyShouldBe('color', 'white')
                ->propertyShouldNotBe('height', '105px'),
            $this->createCssAssertBuilder('.email-header-info-title')
                ->hasFont(new ThemeOptionFont(
                    FontFamilyEnum::arial(),
                    NotificationThemeOptions::DEFAULT_HEADER_SIZE
                ))
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_HEADER),
            $this->createCssAssertBuilder('.email-header-info-date')
                ->propertyShouldBe('color', ThemeOptionColorsBackground::DEFAULT_ACCENT),
        ]);

        $this->assertCssRender(ThemeTypeEnum::plain(), [
            $this->createCssAssertBuilder('.email-header')
                ->propertyShouldBe('color', 'white')
                ->propertyShouldBe('height', '105px'),
            $this->createCssAssertBuilder('.email-header-info-title')
                ->hasFont(new ThemeOptionFont(
                    FontFamilyEnum::calibri(),
                    10,
                    new ThemeOptionFontStyle(true, true, true)
                ))
                ->propertyShouldBe('color', 'rgba(124, 45, 56, 0.33)'),
            $this->createCssAssertBuilder('.email-header-info-date')
                ->propertyShouldBe('color', 'rgba(123, 44, 55, 0.32)'),
        ], [
            'header:imageUrl'          => 'http://pic.com',
            'colors:background:accent' => 'rgba(123, 44, 55, 0.32)',
            'colors:text:header'       => 'rgba(124, 45, 56, 0.33)',

            'fonts:header:family'          => FontFamilyEnum::calibri(),
            'fonts:header:size'            => 10,
            'fonts:header:style:bold'      => true,
            'fonts:header:style:italic'    => true,
            'fonts:header:style:underline' => true,
        ]);
    }

    /**
     * @return void
     */
    public function testTableOfContents()
    {
        $this->tableOfContentsDefault();
        $this->tableOfContentsCustom();
    }

    /**
     * @return void
     */
    public function testContents()
    {
        $this->contentsDefault();
        $this->contentsCustom();
    }

    /**
     * @return void
     */
    public function testFooter()
    {
        $this->createAsserter(ThemeTypeEnum::plain())
            ->with('footer')
                ->has('border-top', '3px double #fff')
            ->end();

        $this->createAsserter(ThemeTypeEnum::plain())
            ->with('footer')
                ->hasNot('border-top')
            ->end();
    }

    /**
     * @return void
     */
    private function tableOfContentsDefault()
    {
        $defaultFont = new ThemeOptionFont(
            FontFamilyEnum::arial(),
            NotificationThemeOptions::DEFAULT_TABLE_OF_CONTENTS_SIZE
        );

        $this->assertCssRender(ThemeTypeEnum::plain(), [
            // .table-of-contents
            $this->createCssAssertBuilder('.table-of-contents .feeds li')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents .feeds > li')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents .feeds > li:before')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents .documents > li:before')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents li:before')
                ->propertyShouldBe('font-size', NotificationThemeOptions::DEFAULT_TABLE_OF_CONTENTS_SIZE)
                ->propertyShouldNotBe('font-weight', 'bold')
                ->propertyShouldNotBe('font-style', 'italic')
                ->propertyShouldBe('text-decoration', 'none'),

            // .feed-name
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-name')
                ->propertyShouldNotBe('width', '48%')
                ->propertyShouldNotBe('display', 'inline-block')
                ->propertyShouldNotBe('margin-left', '20px')
                ->hasFont($defaultFont),

            // .feed-document-count
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-document-count')
                ->propertyShouldNotBe('width', '48%')
                ->propertyShouldNotBe('display', 'inline-block')
                ->hasFont($defaultFont),

            // Document link
            $this->createCssAssertBuilder('.table-of-contents .documents .document a')
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_ARTICLE_HEADLINE)
                ->hasFont($defaultFont),

            // Misc
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-document-count:before')
                ->propertyShouldBe('content', ' '),
            $this->createCssAssertBuilder('.table-of-contents .documents .document a:after')
                ->propertyShouldBe('content', 'url(data:image')
                ->propertyShouldBe('padding-left', '3px'),
            $this->createCssAssertBuilder('.table-of-contents .documents .document .source')
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_SOURCE),
        ]);

        $this->assertCssRender(ThemeTypeEnum::enhanced(), [
            // .table-of-contents
            $this->createCssAssertBuilder('.table-of-contents .feeds li')
                ->propertyShouldBe('background', 'white')
                ->propertyShouldBe('display', 'block')
                ->propertyShouldBe('padding', '5px 10px'),
            $this->createCssAssertBuilder('.table-of-contents .feeds > li')
                ->propertyShouldBe('margin-bottom', '1px')
                ->propertyShouldBe('border-bottom', '1px solid #e6e6e6'),
            $this->createCssAssertBuilder('.table-of-contents .feeds > li:before')
                ->propertyShouldBe('width', '6px')
                ->propertyShouldBe('height', '8px')
                ->propertyShouldBe('content', 'url(data:image'),
            $this->createCssAssertBuilder('.table-of-contents .documents > li:before')
                ->propertyShouldBe('font-size', NotificationThemeOptions::DEFAULT_ARTICLE_CONTENT_SIZE)
                ->propertyShouldNotBe('font-weight', 'bold')
                ->propertyShouldNotBe('font-style', 'italic')
                ->propertyShouldBe('text-decoration', 'none'),
            $this->createCssAssertBuilder('.table-of-contents li:before')->shouldNotExists(),

            // .feed-name
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-name')
                ->propertyShouldBe('width', '48%')
                ->propertyShouldBe('display', 'inline-block')
                ->propertyShouldBe('margin-left', '20px')
                ->hasFont($defaultFont),

            // .feed-document-count
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-document-count')
                ->propertyShouldBe('width', '48%')
                ->propertyShouldBe('display', 'inline-block')
                ->hasFont($defaultFont),

            // Document link
            $this->createCssAssertBuilder('.table-of-contents .documents .document a')
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_ARTICLE_CONTENT)
                ->hasFont(new ThemeOptionFont(
                    FontFamilyEnum::arial(),
                    NotificationThemeOptions::DEFAULT_ARTICLE_CONTENT_SIZE
                )),

            // Misc
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-document-count:before')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents .documents .document a:after')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents .documents .document .source')
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_SOURCE),
        ]);
    }

    /**
     * @return void
     */
    private function tableOfContentsCustom()
    {
        $customFont = new ThemeOptionFont(
            FontFamilyEnum::calibri(),
            10,
            new ThemeOptionFontStyle(true, true, true)
        );

        $this->assertCssRender(ThemeTypeEnum::plain(), [
            // .table-of-contents
            $this->createCssAssertBuilder('.table-of-contents .feeds li')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents .feeds > li')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents .feeds > li:before')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents .documents > li:before')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents li:before')
                ->propertyShouldBe('font-size', 10)
                ->propertyShouldBe('font-weight', 'bold')
                ->propertyShouldBe('font-style', 'italic')
                ->propertyShouldBe('text-decoration', 'none'),

            // .feed-name
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-name')
                ->propertyShouldNotBe('width', '48%')
                ->propertyShouldNotBe('display', 'inline-block')
                ->propertyShouldNotBe('margin-left', '20px')
                ->hasFont($customFont),

            // .feed-document-count
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-document-count')
                ->propertyShouldNotBe('width', '48%')
                ->propertyShouldNotBe('display', 'inline-block')
                ->hasFont($customFont),

            // Document link
            $this->createCssAssertBuilder('.table-of-contents .documents .document a')
                ->propertyShouldBe('color', 'rgba(123, 44, 55, 0.32)')
                ->hasFont($customFont),

            // Misc
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-document-count:before')
                ->propertyShouldBe('content', ' '),
            $this->createCssAssertBuilder('.table-of-contents .documents .document a:after')
                ->propertyShouldBe('content', 'url(data:image')
                ->propertyShouldBe('padding-left', '3px'),
            $this->createCssAssertBuilder('.table-of-contents .documents .document .source')
                ->propertyShouldBe('color', 'rgba(124, 45, 56, 0.33)'),
        ], [
            'colors:text:articleHeadline' => 'rgba(123, 44, 55, 0.32)',
            'colors:text:source' => 'rgba(124, 45, 56, 0.33)',
            'fonts:tableOfContents:family' => $customFont->getFamily(),
            'fonts:tableOfContents:size' => $customFont->getSize(),
            'fonts:tableOfContents:style:bold' => $customFont->getStyle()->isBold(),
            'fonts:tableOfContents:style:italic' => $customFont->getStyle()->isItalic(),
            'fonts:tableOfContents:style:underline' => $customFont->getStyle()->isUnderline(),
        ]);

        $this->assertCssRender(ThemeTypeEnum::enhanced(), [
            // .table-of-contents
            $this->createCssAssertBuilder('.table-of-contents .feeds li')
                ->propertyShouldBe('background', 'white')
                ->propertyShouldBe('display', 'block')
                ->propertyShouldBe('padding', '5px 10px'),
            $this->createCssAssertBuilder('.table-of-contents .feeds > li')
                ->propertyShouldBe('margin-bottom', '1px')
                ->propertyShouldBe('border-bottom', '1px solid #e6e6e6'),
            $this->createCssAssertBuilder('.table-of-contents .feeds > li:before')
                ->propertyShouldBe('width', '6px')
                ->propertyShouldBe('height', '8px')
                ->propertyShouldBe('content', 'url(data:image'),
            $this->createCssAssertBuilder('.table-of-contents .documents > li:before')
                ->propertyShouldBe('font-size', 11)
                ->propertyShouldNotBe('font-weight', 'bold')
                ->propertyShouldBe('font-style', 'italic')
                ->propertyShouldBe('text-decoration', 'none'),
            $this->createCssAssertBuilder('.table-of-contents li:before')->shouldNotExists(),

            // .feed-name
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-name')
                ->propertyShouldBe('width', '48%')
                ->propertyShouldBe('display', 'inline-block')
                ->propertyShouldBe('margin-left', '20px')
                ->hasFont($customFont),

            // .feed-document-count
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-document-count')
                ->propertyShouldBe('width', '48%')
                ->propertyShouldBe('display', 'inline-block')
                ->hasFont($customFont),

            // Document link
            $this->createCssAssertBuilder('.table-of-contents .documents .document a')
                ->propertyShouldBe('color', 'rgba(123, 44, 55, 0.32)')
                ->hasFont(new ThemeOptionFont(
                    FontFamilyEnum::courierNew(),
                    11,
                    new ThemeOptionFontStyle(false, true, true)
                )),

            // Misc
            $this->createCssAssertBuilder('.table-of-contents .feeds .feed .feed-document-count:before')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents .documents .document a:after')->shouldNotExists(),
            $this->createCssAssertBuilder('.table-of-contents .documents .document .source')
                ->propertyShouldBe('color', 'rgba(124, 45, 56, 0.33)'),
        ], [
            'colors:text:articleContent' => 'rgba(123, 44, 55, 0.32)',
            'colors:text:source' => 'rgba(124, 45, 56, 0.33)',

            'fonts:tableOfContents:family' => $customFont->getFamily(),
            'fonts:tableOfContents:size' => $customFont->getSize(),
            'fonts:tableOfContents:style:bold' => $customFont->getStyle()->isBold(),
            'fonts:tableOfContents:style:italic' => $customFont->getStyle()->isItalic(),
            'fonts:tableOfContents:style:underline' => $customFont->getStyle()->isUnderline(),

            'fonts:articleContent:family' => FontFamilyEnum::courierNew(),
            'fonts:articleContent:size' => 11,
            'fonts:articleContent:style:bold' => false,
            'fonts:articleContent:style:italic' => true,
            'fonts:articleContent:style:underline' => true,
        ]);
    }

    /**
     * @return void
     */
    private function contentsDefault()
    {
        //
        // Plain
        //
        $feedTitleFont = new ThemeOptionFont(
            FontFamilyEnum::arial(),
            NotificationThemeOptions::DEFAULT_FEED_TITLE_SIZE
        );
        $dateFont = new ThemeOptionFont(
            FontFamilyEnum::arial(),
            NotificationThemeOptions::DEFAULT_DATE_SIZE
        );
        $articleContentFont = new ThemeOptionFont(
            FontFamilyEnum::arial(),
            NotificationThemeOptions::DEFAULT_ARTICLE_CONTENT_SIZE
        );
        $articleHeadlineFont = new ThemeOptionFont(
            FontFamilyEnum::arial(),
            NotificationThemeOptions::DEFAULT_ARTICLE_HEADLINE_SIZE
        );
        $sourceFont = new ThemeOptionFont(
            FontFamilyEnum::arial(),
            NotificationThemeOptions::DEFAULT_SOURCE_SIZE
        );
        $authorFont = new ThemeOptionFont(
            FontFamilyEnum::arial(),
            NotificationThemeOptions::DEFAULT_AUTHOR_SIZE
        );

        $this->assertCssRender(ThemeTypeEnum::plain(), [
            // .feed-title
            $this->createCssAssertBuilder('.content .feed-title')
                ->hasFont($feedTitleFont)
                ->propertyShouldNotExists('background')
                ->propertyShouldNotExists('color'),

            // .document
            $this->createCssAssertBuilder('.content .documents .document')
                ->propertyShouldBe('margin-top', '10px')
                ->propertyShouldBe('margin-left', '5px')
                ->propertyShouldNotExists('display')
                ->propertyShouldNotExists('flex'),
            $this->createCssAssertBuilder('.content .documents .document:last-child'),
            $this->createCssAssertBuilder('.content .documents .document-aside')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .documents .document-main')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .documents .document-body')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .documents .document-image')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .documents .document-image img')->shouldNotExists(),

            // .document-headline link
            $this->createCssAssertBuilder('.content .documents .document-headline a')
                ->hasFont($articleHeadlineFont)
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_ARTICLE_HEADLINE),

            // .document-source
            $this->createCssAssertBuilder('.content .documents .document-source')
                ->hasFont($sourceFont)
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_SOURCE),

            // .document-author
            $this->createCssAssertBuilder('.content .documents .document-author')
                ->hasFont($authorFont)
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_AUTHOR),

            // .document-date
            $this->createCssAssertBuilder('.content .documents .document-date')
                ->hasFont($dateFont)
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_PUBLISH_DATE),

            // .document-content
            $this->createCssAssertBuilder('.content .document .document-content')
                ->hasFont($articleContentFont),

            // Comments
            $this->createCssAssertBuilder('.content .comments .comment-title')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .comments .comment-author')
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_AUTHOR)
                ->hasFont($authorFont),
            $this->createCssAssertBuilder('.content .comments .comment-date')
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_PUBLISH_DATE)
                ->hasFont($dateFont),
        ]);

        //
        // Enhanced
        //

        $this->assertCssRender(ThemeTypeEnum::enhanced(), [
            // .feed-title
            $this->createCssAssertBuilder('.content .feed-title')
                ->hasFont($feedTitleFont)
                ->propertyShouldBe('background', ThemeOptionColorsBackground::DEFAULT_ACCENT)
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_HEADER),

            // .document
            $this->createCssAssertBuilder('.content .documents .document')
                ->propertyShouldBe('margin-top', '5px')
                ->propertyShouldNotExists('margin-left', '5px')
                ->propertyShouldExists('display')
                ->propertyShouldExists('flex'),
            $this->createCssAssertBuilder('.content .documents .document:last-child')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .documents .document-aside'),
            $this->createCssAssertBuilder('.content .documents .document-main'),
            $this->createCssAssertBuilder('.content .documents .document-body'),
            $this->createCssAssertBuilder('.content .documents .document-image'),
            $this->createCssAssertBuilder('.content .documents .document-image img'),

            // .document-headline link
            $this->createCssAssertBuilder('.content .documents .document-headline a')
                ->hasFont($articleHeadlineFont)
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_ARTICLE_HEADLINE),

            // .document-source
            $this->createCssAssertBuilder('.content .documents .document-source')
                ->hasFont($sourceFont)
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_SOURCE),

            // .document-author
            $this->createCssAssertBuilder('.content .documents .document-author')
                ->hasFont($authorFont)
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_AUTHOR),

            // .document-date
            $this->createCssAssertBuilder('.content .documents .document-date')
                ->hasFont($articleContentFont)
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_ARTICLE_CONTENT),

            // .document-content
            $this->createCssAssertBuilder('.content .document .document-content')
                ->hasFont($articleContentFont),

            // Comments
            $this->createCssAssertBuilder('.content .comments .comment-title'),
            $this->createCssAssertBuilder('.content .comments .comment-author')
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_AUTHOR)
                ->hasNotAnyFonts(),
            $this->createCssAssertBuilder('.content .comments .comment-date')
                ->propertyShouldBe('color', ThemeOptionColorsText::DEFAULT_ARTICLE_CONTENT)
                ->hasNotAnyFonts(),
        ]);
    }

    /**
     * @return void
     */
    private function contentsCustom()
    {
        $feedTitleFont = new ThemeOptionFont(
            FontFamilyEnum::calibri(),
            11,
            new ThemeOptionFontStyle(true, true, true)
        );
        $dateFont = new ThemeOptionFont(
            FontFamilyEnum::centuryGothic(),
            12,
            new ThemeOptionFontStyle(true, false, true)
        );
        $articleContentFont = new ThemeOptionFont(
            FontFamilyEnum::georgia(),
            13,
            new ThemeOptionFontStyle(true, true, false)
        );
        $articleHeadlineFont = new ThemeOptionFont(
            FontFamilyEnum::lucidaSansUnicode(),
            14,
            new ThemeOptionFontStyle(false, true, true)
        );
        $sourceFont = new ThemeOptionFont(
            FontFamilyEnum::courierNew(),
            15,
            new ThemeOptionFontStyle(false, true, false)
        );
        $authorFont = new ThemeOptionFont(
            FontFamilyEnum::tahoma(),
            16,
            new ThemeOptionFontStyle(true, true, false)
        );

        $diffs = [
            'colors:background:accent' => 'rgba(123, 44, 55, 0.32)',

            'colors:text:header' => 'rgba(124, 45, 56, 0.33)',
            'colors:text:publishDate' => 'rgba(125, 46, 57, 0.34)',
            'colors:text:articleContent' => 'rgba(126, 47, 58, 0.35)',
            'colors:text:articleHeadline' => 'rgba(127, 48, 59, 0.36)',
            'colors:text:source' => 'rgba(128, 49, 60, 0.37)',
            'colors:text:author' => 'rgba(129, 50, 61, 0.38)',

            'fonts:feedTitle:family' => $feedTitleFont->getFamily(),
            'fonts:feedTitle:size' => $feedTitleFont->getSize(),
            'fonts:feedTitle:style:bold' => $feedTitleFont->getStyle()->isBold(),
            'fonts:feedTitle:style:italic' => $feedTitleFont->getStyle()->isItalic(),
            'fonts:feedTitle:style:underline' => $feedTitleFont->getStyle()->isUnderline(),

            'fonts:date:family' => $dateFont->getFamily(),
            'fonts:date:size' => $dateFont->getSize(),
            'fonts:date:style:bold' => $dateFont->getStyle()->isBold(),
            'fonts:date:style:italic' => $dateFont->getStyle()->isItalic(),
            'fonts:date:style:underline' => $dateFont->getStyle()->isUnderline(),

            'fonts:articleContent:family' => $articleContentFont->getFamily(),
            'fonts:articleContent:size' => $articleContentFont->getSize(),
            'fonts:articleContent:style:bold' => $articleContentFont->getStyle()->isBold(),
            'fonts:articleContent:style:italic' => $articleContentFont->getStyle()->isItalic(),
            'fonts:articleContent:style:underline' => $articleContentFont->getStyle()->isUnderline(),

            'fonts:articleHeadline:family' => $articleHeadlineFont->getFamily(),
            'fonts:articleHeadline:size' => $articleHeadlineFont->getSize(),
            'fonts:articleHeadline:style:bold' => $articleHeadlineFont->getStyle()->isBold(),
            'fonts:articleHeadline:style:italic' => $articleHeadlineFont->getStyle()->isItalic(),
            'fonts:articleHeadline:style:underline' => $articleHeadlineFont->getStyle()->isUnderline(),

            'fonts:source:family' => $sourceFont->getFamily(),
            'fonts:source:size' => $sourceFont->getSize(),
            'fonts:source:style:bold' => $sourceFont->getStyle()->isBold(),
            'fonts:source:style:italic' => $sourceFont->getStyle()->isItalic(),
            'fonts:source:style:underline' => $sourceFont->getStyle()->isUnderline(),

            'fonts:author:family' => $authorFont->getFamily(),
            'fonts:author:size' => $authorFont->getSize(),
            'fonts:author:style:bold' => $authorFont->getStyle()->isBold(),
            'fonts:author:style:italic' => $authorFont->getStyle()->isItalic(),
            'fonts:author:style:underline' => $authorFont->getStyle()->isUnderline(),
        ];

        //
        // Plain
        //
        $this->assertCssRender(ThemeTypeEnum::plain(), [
            // .feed-title
            $this->createCssAssertBuilder('.content .feed-title')
                ->hasFont($feedTitleFont)
                ->propertyShouldNotExists('background')
                ->propertyShouldNotExists('color'),

            // .document
            $this->createCssAssertBuilder('.content .documents .document')
                ->propertyShouldBe('margin-top', '10px')
                ->propertyShouldBe('margin-left', '5px')
                ->propertyShouldNotExists('display')
                ->propertyShouldNotExists('flex'),
            $this->createCssAssertBuilder('.content .documents .document:last-child'),
            $this->createCssAssertBuilder('.content .documents .document-aside')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .documents .document-main')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .documents .document-body')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .documents .document-image')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .documents .document-image img')->shouldNotExists(),

            // .document-headline link
            $this->createCssAssertBuilder('.content .documents .document-headline a')
                ->hasFont($articleHeadlineFont)
                ->propertyShouldBe('color', 'rgba(127, 48, 59, 0.36)'),

            // .document-source
            $this->createCssAssertBuilder('.content .documents .document-source')
                ->hasFont($sourceFont)
                ->propertyShouldBe('color', 'rgba(128, 49, 60, 0.37)'),

            // .document-author
            $this->createCssAssertBuilder('.content .documents .document-author')
                ->hasFont($authorFont)
                ->propertyShouldBe('color', 'rgba(129, 50, 61, 0.38)'),

            // .document-date
            $this->createCssAssertBuilder('.content .documents .document-date')
                ->hasFont($dateFont)
                ->propertyShouldBe('color', 'rgba(125, 46, 57, 0.34)'),

            // .document-content
            $this->createCssAssertBuilder('.content .document .document-content')
                ->hasFont($articleContentFont),

            // Comments
            $this->createCssAssertBuilder('.content .comments .comment-title')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .comments .comment-author')
                ->propertyShouldBe('color', 'rgba(129, 50, 61, 0.38)')
                ->hasFont($authorFont),
            $this->createCssAssertBuilder('.content .comments .comment-date')
                ->propertyShouldBe('color', 'rgba(125, 46, 57, 0.34)')
                ->hasFont($dateFont),
        ], $diffs);

        //
        // Enhanced
        //

        $this->assertCssRender(ThemeTypeEnum::enhanced(), [
            // .feed-title
            $this->createCssAssertBuilder('.content .feed-title')
                ->hasFont($feedTitleFont)
                ->propertyShouldBe('background', 'rgba(123, 44, 55, 0.32)')
                ->propertyShouldBe('color', 'rgba(124, 45, 56, 0.33)'),

            // .document
            $this->createCssAssertBuilder('.content .documents .document')
                ->propertyShouldBe('margin-top', '5px')
                ->propertyShouldNotExists('margin-left', '5px')
                ->propertyShouldExists('display')
                ->propertyShouldExists('flex'),
            $this->createCssAssertBuilder('.content .documents .document:last-child')->shouldNotExists(),
            $this->createCssAssertBuilder('.content .documents .document-aside'),
            $this->createCssAssertBuilder('.content .documents .document-main'),
            $this->createCssAssertBuilder('.content .documents .document-body'),
            $this->createCssAssertBuilder('.content .documents .document-image'),
            $this->createCssAssertBuilder('.content .documents .document-image img'),

            // .document-headline link
            $this->createCssAssertBuilder('.content .documents .document-headline a')
                ->hasFont($articleHeadlineFont)
                ->propertyShouldBe('color', 'rgba(127, 48, 59, 0.36)'),

            // .document-source
            $this->createCssAssertBuilder('.content .documents .document-source')
                ->hasFont($sourceFont)
                ->propertyShouldBe('color', 'rgba(128, 49, 60, 0.37)'),

            // .document-author
            $this->createCssAssertBuilder('.content .documents .document-author')
                ->hasFont($authorFont)
                ->propertyShouldBe('color', 'rgba(129, 50, 61, 0.38)'),

            // .document-date
            $this->createCssAssertBuilder('.content .documents .document-date')
                ->hasFont($dateFont)
                ->propertyShouldBe('color', 'rgba(126, 47, 58, 0.35)'),

            // .document-content
            $this->createCssAssertBuilder('.content .document .document-content')
                ->hasFont($articleContentFont),

            // Comments
            $this->createCssAssertBuilder('.content .comments .comment-title'),
            $this->createCssAssertBuilder('.content .comments .comment-author')
                ->propertyShouldBe('color', 'rgba(129, 50, 61, 0.38)')
                ->hasNotAnyFonts(),
            $this->createCssAssertBuilder('.content .comments .comment-date')
                ->propertyShouldBe('color', 'rgba(126, 47, 58, 0.35)')
                ->hasNotAnyFonts(),
        ], $diffs);
    }

    /**
     * @param string  $selector A base css element selector.
     * @param boolean $escape   Should escape specific pattern symbols or not.
     *
     * @return CssAssertBuilder
     */
    private function createCssAssertBuilder($selector, $escape = true)
    {
        return new CssAssertBuilder($selector, $escape);
    }

    /**
     * @param ThemeTypeEnum      $themeType A ThemeTypeEnum instance.
     * @param CssAssertBuilder[] $asserts   Array of css assert builders.
     *
     * @param array              $diffs     Notification theme diffs.
     *
     * @return void
     */
    private function assertCssRender(ThemeTypeEnum $themeType, array $asserts, array $diffs = [])
    {
        $html = $this->render($themeType, $diffs, [ new FeedData('test', []) ]);

        /** @var CssAssertBuilder $assert */
        foreach ($asserts as $assert) {
            $assert->assert($html);
        }
    }

    /**
     * @param ThemeTypeEnum $themeType A ThemeTypeEnum instance.
     * @param array         $diffs     Notification theme diffs.
     *
     * @return CssAsserter
     */
    private function createAsserter(ThemeTypeEnum $themeType, array $diffs = [])
    {
        return CssAsserter::createFromHtml($this->render($themeType, $diffs, [ new FeedData('test', []) ]));
    }
}
