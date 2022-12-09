<?php

namespace UserBundle\Manager\Notification;

use CacheBundle\Entity\Comment;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Helper\HtmlAsserter;
use UserBundle\Entity\User;
use UserBundle\Enum\ThemeOptionsTableOfContentsEnum;
use UserBundle\Enum\ThemeOptionsUserCommentsEnum;
use UserBundle\Enum\ThemeTypeEnum;
use UserBundle\Manager\Notification\Model\FeedData;
use IndexBundle\Model\ArticleDocument;

/**
 * Class NotificationContentRenderTest
 *
 * @package UserBundle\Manager\Notification
 */
class NotificationContentRenderTest extends AbstractSendableNotificationTest
{

    const FIRST_FEED_COUNT = 1;
    const SECOND_FEED_COUNT = 1;
    const THIRD_FEED_COUNT = 0;

    /**
     * @return void
     */
    public function testSummaryAndConclusion()
    {
        //
        // Plain
        //
        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::simple(),
        ])
            ->hasNotNode('.email-summary')
            ->hasNotNode('.email-conclusion');

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'summary' => 'summary text',
            'conclusion' => 'conclusion text',
        ])
            ->with('.email-summary')->contains('summary text')->end()
            ->with('.email-conclusion')->contains('conclusion text')->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'conclusion' => 'conclusion text',
        ])
            ->hasNotNode('.email-summary')
            ->with('.email-conclusion')->contains('conclusion text')->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'summary' => 'summary text',
        ])
            ->with('.email-summary')->contains('summary text')->end()
            ->hasNotNode('.email-conclusion');

        //
        // Enhanced
        //
        $this->createAsserter(ThemeTypeEnum::enhanced())
            ->hasNotNode('.email-summary')
            ->hasNotNode('.email-conclusion');

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'summary' => 'summary text',
            'conclusion' => 'conclusion text',
        ])
            ->with('.email-summary')->contains('summary text')->end()
            ->with('.email-conclusion')->contains('conclusion text')->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'conclusion' => 'conclusion text',
        ])
            ->hasNotNode('.email-summary')
            ->with('.email-conclusion')->contains('conclusion text')->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'summary' => 'summary text',
        ])
            ->with('.email-summary')->contains('summary text')->end()
            ->hasNotNode('.email-conclusion');
    }

    /**
     * @return void
     */
    public function testTableOfContentsPlain()
    {
        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::no(),
            'content:showInfo:articleCount' => true,
        ])
            ->hasNotNode('.table-of-contents')->end()
            ->hasNotNode('.table-of-contents .feed-document-count')->end();


        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::no(),
            'content:showInfo:articleCount' => false,
        ])
            ->hasNotNode('.table-of-contents')->end()
            ->hasNotNode('.table-of-contents .feed-document-count')->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::simple(),
            'content:showInfo:articleCount' => true,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->with('.feed-document-count')
                    ->child(0)->contains(sprintf('(%d articles)', self::FIRST_FEED_COUNT))->end()
                    ->child(1)->contains(sprintf('(%d articles)', self::SECOND_FEED_COUNT))->end()
                    ->child(2)->contains(sprintf('(%d articles)', self::THIRD_FEED_COUNT))->end()
                ->end()
                ->hasNotNode('.documents .document')
            ->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::simple(),
            'content:showInfo:articleCount' => false,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->hasNotNode('.feed-document-count')
                ->hasNotNode('.documents .document')
            ->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::headline(),
            'content:showInfo:articleCount' => true,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->with('.feed-document-count')
                    ->child(0)->contains(sprintf('(%d articles)', self::FIRST_FEED_COUNT))->end()
                    ->child(1)->contains(sprintf('(%d articles)', self::SECOND_FEED_COUNT))->end()
                    ->child(2)->contains(sprintf('(%d articles)', self::THIRD_FEED_COUNT))->end()
                ->end()
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('Feed1 Document1', true)
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::headline(),
            'content:showInfo:articleCount' => false,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->hasNotNode('.feed-document-count')
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('Feed1 Document1', true)
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::headlineSourceDate(),
            'content:showInfo:articleCount' => true,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->with('.feed-document-count')
                    ->child(0)->contains(sprintf('(%d articles)', self::FIRST_FEED_COUNT))->end()
                    ->child(1)->contains(sprintf('(%d articles)', self::SECOND_FEED_COUNT))->end()
                    ->child(2)->contains(sprintf('(%d articles)', self::THIRD_FEED_COUNT))->end()
                ->end()
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('Feed1 Document1 | CNN | January 01, 2017')
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::headlineSourceDate(),
            'content:showInfo:articleCount' => false,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->hasNotNode('.feed-document-count')
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('Feed1 Document1 | CNN | January 01, 2017')
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::sourceHeadlineDate(),
            'content:showInfo:articleCount' => true,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->with('.feed-document-count')
                    ->child(0)->contains(sprintf('(%d articles)', self::FIRST_FEED_COUNT))->end()
                    ->child(1)->contains(sprintf('(%d articles)', self::SECOND_FEED_COUNT))->end()
                    ->child(2)->contains(sprintf('(%d articles)', self::THIRD_FEED_COUNT))->end()
                ->end()
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('CNN | Feed1 Document1 | January 01, 2017')
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::sourceHeadlineDate(),
            'content:showInfo:articleCount' => false,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->hasNotNode('.feed-document-count')
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('CNN | Feed1 Document1 | January 01, 2017')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @return void
     */
    public function testTableOfContainsEnhanced()
    {
        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::no(),
            'content:showInfo:articleCount' => true,
        ])
            ->hasNotNode('.table-of-contents')->end()
            ->hasNotNode('.table-of-contents .feed-document-count')->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::no(),
            'content:showInfo:articleCount' => false,
        ])
            ->hasNotNode('.table-of-contents')->end()
            ->hasNotNode('.table-of-contents .feed-document-count')->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::simple(),
            'content:showInfo:articleCount' => true,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->with('.feed-document-count')
                    ->child(0)->contains(sprintf('%d articles', self::FIRST_FEED_COUNT))->end()
                    ->child(1)->contains(sprintf('%d articles', self::SECOND_FEED_COUNT))->end()
                    ->child(2)->contains(sprintf('%d articles', self::THIRD_FEED_COUNT))->end()
                ->end()
                ->hasNotNode('.documents .document')
            ->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::simple(),
            'content:showInfo:articleCount' => false,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->hasNotNode('.feed-document-count')
                ->hasNotNode('.documents .document')
            ->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::headline(),
            'content:showInfo:articleCount' => true,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->with('.feed-document-count')
                    ->child(0)->contains(sprintf('%d articles', self::FIRST_FEED_COUNT))->end()
                    ->child(1)->contains(sprintf('%d articles', self::SECOND_FEED_COUNT))->end()
                    ->child(2)->contains(sprintf('%d articles', self::THIRD_FEED_COUNT))->end()
                ->end()
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('Feed1 Document1', true)
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::headline(),
            'content:showInfo:articleCount' => false,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->hasNotNode('.feed-document-count')
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('Feed1 Document1', true)
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::headlineSourceDate(),
            'content:showInfo:articleCount' => true,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->with('.feed-document-count')
                    ->child(0)->contains(sprintf('%d articles', self::FIRST_FEED_COUNT))->end()
                    ->child(1)->contains(sprintf('%d articles', self::SECOND_FEED_COUNT))->end()
                    ->child(2)->contains(sprintf('%d articles', self::THIRD_FEED_COUNT))->end()
                ->end()
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('Feed1 Document1 | CNN | January 01, 2017')
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::headlineSourceDate(),
            'content:showInfo:articleCount' => false,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->hasNotNode('.feed-document-count')
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('Feed1 Document1 | CNN | January 01, 2017')
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::sourceHeadlineDate(),
            'content:showInfo:articleCount' => true,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->with('.feed-document-count')
                    ->child(0)->contains(sprintf('%d articles', self::FIRST_FEED_COUNT))->end()
                    ->child(1)->contains(sprintf('%d articles', self::SECOND_FEED_COUNT))->end()
                    ->child(2)->contains(sprintf('%d articles', self::THIRD_FEED_COUNT))->end()
                ->end()
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('CNN | Feed1 Document1 | January 01, 2017')
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:tableOfContents' => ThemeOptionsTableOfContentsEnum::sourceHeadlineDate(),
            'content:showInfo:articleCount' => false,
        ])
            ->with('.table-of-contents')
                ->with('.feed-name')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->hasNotNode('.feed-document-count')
                ->with('.documents .document')
                    ->child(0)
                        ->with('a')
                            ->hasAttr('href', 'http://permalink')
                            ->contains('CNN | Feed1 Document1 | January 01, 2017')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @return void
     */
    public function testContentPlain()
    {
        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:sectionDivider' => false,
            'content:showInfo:sourceCountry' => false,
            'content:showInfo:userComments' => ThemeOptionsUserCommentsEnum::no(),
            'content:showInfo:images' => false,
        ])
            ->with('.content')
                ->with('.feed-title')
                    ->child(0)->contains('feed1:')->end()
                    ->child(1)->contains('feed2:')->end()
                    ->child(2)->contains('feed3:')->end()
                ->end()
                ->hasNotNode('.feed-title img')
                ->notContains('<b>Comments</b>')
                ->with('.documents')
                    ->hasNotNode('.document-aside')
                    ->with('.document')
                        ->child(0)
                            ->with('.document-headline a')
                                ->hasAttr('href', 'http://permalink')
                                ->contains('Feed1 Document1')
                            ->end()
                            ->with('.document-source')
                                ->contains('CNN')
                                ->notContains('(Russian)')
                            ->end()
                            ->with('.document-author')->contains('John Smith')->end()
                            ->with('.document-date')
                                ->contains('-')
                                ->contains(date_create('2017-01-01 10:00:00')->format('F d, Y H:i'))
                            ->end()
                            ->with('.document-content')->contains('Feed1 Document1 Main')->end()
                            ->hasNotNode('.comments')
                            ->hasNotNode('.document-image')
                        ->end()
                        ->child(1)
                            ->with('.document-headline a')
                                ->hasAttr('href', 'http://permalink_next')
                                ->contains('Feed2 Document1')
                            ->end()
                            ->with('.document-source')
                                ->contains('Test')
                                ->notContains('(USA)')
                            ->end()
                            ->hasNotNode('.document-author')
                            ->with('.document-date')
                                ->contains('-')
                                ->contains(date_create('2017-01-10 10:00:00')->format('F d, Y H:i'))
                            ->end()
                            ->with('.document-content')->contains('Feed2 Document1 Main')->end()
                            ->hasNotNode('.comments')
                            ->hasNotNode('.document-image')
                        ->end()
                    ->end()
                ->end()
                ->hasNotNode('.feed-divider')
            ->end();


        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:sectionDivider' => true,
            'content:showInfo:sourceCountry' => true,
            'content:showInfo:userComments' => ThemeOptionsUserCommentsEnum::withAuthorDate(),
            'content:showInfo:images' => true,
        ])
            ->with('.content')
                ->with('.feed-title')
                    ->child(0)->contains('feed1:')->end()
                    ->child(1)->contains('feed2:')->end()
                    ->child(2)->contains('feed3:')->end()
                ->end()
                ->hasNotNode('.feed-title img')
                ->notContains('<b>Comments</b>')
                ->with('.documents')
                    ->hasNotNode('.document-aside')
                    ->with('.document')
                        ->child(0)
                            ->with('.document-headline a')
                                ->hasAttr('href', 'http://permalink')
                                ->contains('Feed1 Document1')
                            ->end()
                            ->with('.document-source')
                                ->contains('CNN')
                                ->contains('(Russian)')
                            ->end()
                            ->with('.document-author')->contains('John Smith')->end()
                            ->with('.document-date')
                                ->contains('-')
                                ->contains(date_create('2017-01-01 10:00:00')->format('F d, Y H:i'))
                            ->end()
                            ->with('.document-content')->contains('Feed1 Document1 Main')->end()
                            ->hasNotNode('.comments')
                            ->hasNotNode('.document-image')
                        ->end()
                        ->child(1)
                            ->with('.document-headline a')
                                ->hasAttr('href', 'http://permalink_next')
                                ->contains('Feed2 Document1')
                            ->end()
                            ->with('.document-source')->contains('Test')->end()
                            ->hasNotNode('.document-author')
                            ->with('.document-date')
                                ->contains('-')
                                ->contains(date_create('2017-01-10 10:00:00')->format('F d, Y H:i'))
                            ->end()
                            ->with('.document-content')->contains('Feed2 Document1 Main')->end()
                            ->with('.comments .comment')
                                ->child(0)
                                    ->with('.comment-title')->contains('Feed2 Document1 comment1 title')->end()
                                    ->with('.comment-author')
                                        ->contains('User1 first name')
                                        ->contains('User1 last name')
                                    ->end()
                                    ->with('.comment-date')
                                        ->contains(date_create('2017-01-02 10:00:00')->format('F d, Y H:i'))
                                    ->end()
                                    ->with('.comment-body')->contains('Feed2 Document1 comment1')->end()
                                ->end()
                                ->child(1)
                                    ->hasNotNode('.comment-title')
                                    ->with('.comment-author')
                                        ->contains('User2 first name')
                                        ->contains('User2 last name')
                                    ->end()
                                    ->with('.comment-date')
                                        ->contains(date_create('2017-01-03 10:00:00')->format('F d, Y H:i'))
                                    ->end()
                                    ->with('.comment-body')->contains('Feed2 Document1 comment2')->end()
                                ->end()
                            ->end()
                            ->hasNotNode('.document-image')
                        ->end()
                    ->end()
                ->end()
                ->hasNode('.feed-divider', 2)
            ->end();

        $this->createAsserter(ThemeTypeEnum::plain(), [
            'content:showInfo:userComments' => ThemeOptionsUserCommentsEnum::withoutAuthorDate(),
        ])
            ->with('.content')
            ->with('.document')
                ->child(1)
                    ->with('.comments')
                        ->hasNotNode('.comment-date')
                    ->end()
                ->end()
            ->end()
            ->end();
    }

    /**
     * @return void
     */
    public function testContentEnhanced()
    {
        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:sectionDivider' => false,
            'content:showInfo:sourceCountry' => false,
            'content:showInfo:userComments' => ThemeOptionsUserCommentsEnum::no(),
            'content:showInfo:images' => false,
        ])
            ->with('.content')
                ->with('.feed-title')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->hasNode('.feed-title img', 3)
                ->with('.documents')
                    ->hasNode('.document-aside', 2)
                    ->with('.document')
                        ->child(0)
                            ->with('.document-headline a')
                                ->hasAttr('href', 'http://permalink')
                                ->contains('Feed1 Document1')
                            ->end()
                            ->with('.document-source')
                                ->contains('CNN')
                                ->notContains('(Russian)')
                            ->end()
                            ->with('.document-author')->contains('John Smith')->end()
                            ->with('.document-date')
                                ->contains('|')
                                ->contains(date_create('2017-01-01 10:00:00')->format('F d, Y H:i'))
                            ->end()
                            ->with('.document-content')->contains('Feed1 Document1 Main')->end()
                            ->hasNotNode('.comments')
                            ->hasNotNode('.document-image')
                        ->end()
                        ->child(1)
                            ->with('.document-headline a')
                                ->hasAttr('href', 'http://permalink_next')
                                ->contains('Feed2 Document1')
                            ->end()
                            ->with('.document-source')
                                ->contains('Test')
                                ->notContains('(USA)')
                            ->end()
                            ->hasNotNode('.document-author')
                            ->with('.document-date')
                                ->contains('|')
                                ->contains(date_create('2017-01-10 10:00:00')->format('F d, Y H:i'))
                            ->end()
                            ->with('.document-content')->contains('Feed2 Document1 Main')->end()
                            ->hasNotNode('.comments')
                            ->hasNotNode('.document-image')
                        ->end()
                    ->end()
                ->end()
                ->hasNotNode('.feed-divider')
            ->end();


        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:sectionDivider' => true,
            'content:showInfo:sourceCountry' => true,
            'content:showInfo:userComments' => ThemeOptionsUserCommentsEnum::withAuthorDate(),
            'content:showInfo:images' => true,
        ])
            ->with('.content')
                ->with('.feed-title')
                    ->child(0)->contains('feed1')->end()
                    ->child(1)->contains('feed2')->end()
                    ->child(2)->contains('feed3')->end()
                ->end()
                ->hasNode('.feed-title img', 3)
                ->with('.documents')
                    ->hasNode('.document-aside', 2)
                    ->with('.document')
                        ->child(0)
                            ->with('.document-headline a')
                                ->hasAttr('href', 'http://permalink')
                                ->contains('Feed1 Document1')
                            ->end()
                            ->with('.document-source')
                                ->contains('CNN')
                                ->contains('(Russian)')
                            ->end()
                            ->with('.document-author')->contains('John Smith')->end()
                            ->with('.document-date')
                                ->contains('|')
                                ->contains(date_create('2017-01-01 10:00:00')->format('F d, Y H:i'))
                            ->end()
                            ->with('.document-content')->contains('Feed1 Document1 Main')->end()
                            ->hasNotNode('.comments')
                            ->hasNode('.document-image')
                        ->end()
                        ->child(1)
                            ->with('.document-headline a')
                                ->hasAttr('href', 'http://permalink_next')
                                ->contains('Feed2 Document1')
                            ->end()
                            ->with('.document-source')->contains('Test')->end()
                            ->hasNotNode('.document-author')
                            ->with('.document-date')
                                ->contains('|')
                                ->contains(date_create('2017-01-10 10:00:00')->format('F d, Y H:i'))
                            ->end()
                            ->with('.document-content')->contains('Feed2 Document1 Main')->end()
                            ->with('.comments .comment')
                                ->child(0)
                                    ->with('.comment-title')->contains('Feed2 Document1 comment1 title')->end()
                                    ->with('.comment-author')
                                        ->contains('User1 first name')
                                        ->contains('User1 last name')
                                    ->end()
                                    ->with('.comment-date')
                                        ->contains(date_create('2017-01-02 10:00:00')->format('F d, Y H:i'))
                                    ->end()
                                    ->with('.comment-body')->contains('Feed2 Document1 comment1')->end()
                                ->end()
                                ->child(1)
                                    ->hasNotNode('.comment-title')
                                    ->with('.comment-author')
                                        ->contains('User2 first name')
                                        ->contains('User2 last name')
                                    ->end()
                                    ->with('.comment-date')
                                        ->contains(date_create('2017-01-03 10:00:00')->format('F d, Y H:i'))
                                    ->end()
                                    ->with('.comment-body')->contains('Feed2 Document1 comment2')->end()
                                ->end()
                            ->end()
                            ->hasNotNode('.document-image')
                        ->end()
                    ->end()
                ->end()
                ->hasNotNode('.feed-divider')
            ->end();

        $this->createAsserter(ThemeTypeEnum::enhanced(), [
            'content:showInfo:userComments' => ThemeOptionsUserCommentsEnum::withoutAuthorDate(),
        ])
            ->with('.content')
                ->with('.document')
                    ->child(1)
                        ->with('.comments')
                            ->hasNotNode('.comment-date')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ThemeTypeEnum $themeType A ThemeTypeEnum instance.
     * @param array         $diffs     Notification theme diffs.
     *
     * @return HtmlAsserter
     */
    private function createAsserter(ThemeTypeEnum $themeType, array $diffs = [])
    {
        $comment1 = new Comment(
            User::create('some@main.com')
                ->setFirstName('User1 first name')
                ->setLastName('User1 last name'),
            'Feed2 Document1 comment1',
            'Feed2 Document1 comment1 title'
        );
        $comment1->setCreatedAt(date_create('2017-01-02 10:00:00'));
        $comment2 = new Comment(
            User::create('some@main.com')
                ->setFirstName('User2 first name')
                ->setLastName('User2 last name'),
            'Feed2 Document1 comment2'
        );
        $comment2->setCreatedAt(date_create('2017-01-03 10:00:00'));

        /** @var IndexStrategyInterface|\PHPUnit_Framework_MockObject_MockObject $strategy */
        $strategy = $this->getMockForInterface(IndexStrategyInterface::class);

        $strategy
            ->method('normalizeDocumentData')
            ->willReturnCallback(function (array $data) {
                return $data;
            });

        $strategy
            ->method('normalizeFieldName')
            ->willReturnCallback(function ($fieldName) {
                return $fieldName;
            });

        $strategy
            ->method('normalizePublisherType')
            ->willReturnCallback(function ($type) {
                return $type;
            });

        $crawler = new Crawler($this->render($themeType, $diffs, [
            new FeedData('feed1', [
                new ArticleDocument($strategy, [
                    'title' => 'Feed1 Document1',
                    'permalink' => 'http://permalink',
                    'content' => 'Feed1 Document1 Main',
                    'published' => date_create('2017-01-01 10:00:00'),
                    'source' => [
                        'title' => 'CNN',
                        'country' => 'Russian',
                    ],
                    'author' => [
                        'name' => 'John Smith',
                    ],
                    'image' => 'http://image.dev',
                ]),
            ]),
            new FeedData('feed2', [
                new ArticleDocument($strategy, [
                    'title' => 'Feed2 Document1',
                    'permalink' => 'http://permalink_next',
                    'content' => 'Feed2 Document1 Main',
                    'published' => date_create('2017-01-10 10:00:00'),
                    'source' => [
                        'title' => 'Test',
                        'country' => 'USA',
                    ],
                    'comments' => [
                        $comment1,
                        $comment2,
                    ],
                ]),
            ]),
            new FeedData('feed3', []),
        ]));

        return new HtmlAsserter($crawler);
    }
}
