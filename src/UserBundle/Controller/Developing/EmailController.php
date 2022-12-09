<?php

namespace UserBundle\Controller\Developing;

use AppBundle\AppBundleServices;
use AppBundle\Configuration\ConfigurationInterface;
use CacheBundle\Entity\Comment;
use CacheBundle\Entity\Document;
use Faker\Factory;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Index\Strategy\HoseIndexStrategy;
use IndexBundle\Model\ArticleDocument;
use IndexBundle\Model\ArticleDocumentInterface;
use IndexBundle\Model\Generator\ExternalDocumentGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Notification\NotificationTheme;
use UserBundle\Entity\Notification\NotificationThemeOptions;
use UserBundle\Entity\User;
use UserBundle\Enum\ThemeOptionsUserCommentsEnum;
use UserBundle\Enum\ThemeTypeEnum;
use UserBundle\Manager\Notification\Model\FeedData;
use UserBundle\Manager\Notification\SendableNotification;
use UserBundle\Manager\Notification\SendableNotificationConfig;

/**
 * Class EmailController
 *
 * For developing ant testing email's only.
 *
 * @package UserBundle\Controller\Developing
 *
 * @Route("/emails")
 */
class EmailController extends Controller
{

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var IndexStrategyInterface
     */
    private $strategy;

    /**
     * EmailController constructor.
     */
    public function __construct()
    {
        $this->strategy = new HoseIndexStrategy();
    }

    /**
     * @Route("/plain", methods={ "GET" })
     *
     * @return Response
     */
    public function plainAction()
    {
        $notification = $this->generateNotification(Notification::create()->setThemeType(ThemeTypeEnum::plain()));

        $notification->setPlainThemeOptionsDiff([
//            'summary' => '<p>Summary</p>',
//            'conclusion' => '<p>Conclusion</p>',
//
//            'header.imageUrl' => ThemeOptionHeader::DEFAULT_IMAGE,
//            'header.logoLink' => 'http://ya.ru',
//
//            'content.showInfo.images' => true,
//            'content.showInfo.sectionDivider' => true,
//            'content.showInfo.sourceCountry' => true,
//            'content.showInfo.articleCount' => true,
//            'content.showInfo.tableOfContents' => ThemeOptionsTableOfContentsEnum::SOURCE_HEADLINE_DATE,
            'content.showInfo.userComments' => ThemeOptionsUserCommentsEnum::WITHOUT_AUTHOR_DATE,
//
//            'colors.text.articleHeadline' => 'yellow',
//            'colors.text.source' => 'green',
//            'colors.text.articleContent' => 'red',
//            'colors.text.author' => '#fdfdfd',
//            'colors.text.publishDate' => '#23fa1f',
//
//            'fonts.tableOfContents.size' => 18,
//            'fonts.tableOfContents.family' => 'Courier New',
//            'fonts.tableOfContents.style.bold' => true,
//            'fonts.tableOfContents.style.italic' => true,
//            'fonts.tableOfContents.style.underline' => true,
//
//            'fonts.feedTitle.size' => 18,
//            'fonts.feedTitle.family' => 'Courier New',
//            'fonts.feedTitle.style.bold' => true,
//            'fonts.feedTitle.style.italic' => true,
//            'fonts.feedTitle.style.underline' => true,
//
//            'fonts.articleHeadline.size' => 10,
//            'fonts.articleHeadline.family' => 'Courier New',
//            'fonts.articleHeadline.style.bold' => true,
//            'fonts.articleHeadline.style.italic' => true,
//            'fonts.articleHeadline.style.underline' => true,
//
//            'fonts.source.size' => 10,
//            'fonts.source.family' => 'Courier New',
//            'fonts.source.style.bold' => true,
//            'fonts.source.style.italic' => true,
//            'fonts.source.style.underline' => true,
//
//            'fonts.author.size' => 6,
//            'fonts.author.family' => 'Courier New',
//            'fonts.author.style.bold' => true,
//            'fonts.author.style.italic' => true,
//            'fonts.author.style.underline' => true,
//
//            'fonts.date.size' => 14,
//            'fonts.date.family' => 'Courier New',
//            'fonts.date.style.bold' => true,
//            'fonts.date.style.italic' => true,
//            'fonts.date.style.underline' => true,
//
//            'fonts.articleContent.size' => 6,
//            'fonts.articleContent.family' => 'Courier New',
//            'fonts.articleContent.style.bold' => true,
//            'fonts.articleContent.style.italic' => true,
//            'fonts.articleContent.style.underline' => true,
        ]);

        /** @var ConfigurationInterface $configuration */
        $configuration = $this->get(AppBundleServices::CONFIGURATION);

        $sendableNotification = new SendableNotification(
            SendableNotificationConfig::fromConfiguration($configuration),
            $notification,
            $this->generateFeeds(3)
        );

        return new Response($sendableNotification->render($this->get('templating')));
    }

    /**
     * @Route("/enhanced", methods={ "GET" })
     *
     * @return Response
     */
    public function enhancedAction()
    {
        $notification = $this->generateNotification(Notification::create()->setThemeType(ThemeTypeEnum::enhanced()));

        $notification->setEnhancedThemeOptionsDiff([
//            'summary' => '<p>Summary</p>',
//            'conclusion' => '<p>Conclusion</p>',
//
//            'header.imageUrl' => ThemeOptionHeader::DEFAULT_IMAGE,
//            'header.logoLink' => 'http://ya.ru',
//
//            'content.showInfo.sectionDivider' => true,
//            'content.showInfo.sourceCountry' => true,
//            'content.showInfo.articleCount' => true,
//            'content.showInfo.tableOfContents' => ThemeOptionsTableOfContentsEnum::HEADLINE_SOURCE_DATE,
            'content.showInfo.userComments' => ThemeOptionsUserCommentsEnum::WITH_AUTHOR_DATE,
//
//            'colors.background.header' => '#541dab',
//            'colors.background.accent' => '#df10bc',
//            'colors.background.emailBody' => '#eeeeee',
//
//            'colors.text.header' => '#12fdfd',
//            'colors.text.articleHeadline' => 'yellow',
//            'colors.text.source' => 'green',
//            'colors.text.articleContent' => 'red',
//            'colors.text.author' => '#fdfdfd',
//            'colors.text.publishDate' => '#23fa1f',
//
//            'fonts.header.size' => 18,
//            'fonts.header.family' => 'Courier New',
//            'fonts.header.style.bold' => true,
//            'fonts.header.style.italic' => true,
//            'fonts.header.style.underline' => true,
//
//            'fonts.tableOfContents.size' => 32,
//            'fonts.tableOfContents.family' => 'Courier New',
//            'fonts.tableOfContents.style.bold' => true,
//            'fonts.tableOfContents.style.italic' => true,
//            'fonts.tableOfContents.style.underline' => true,
//
//            'fonts.feedTitle.size' => 18,
//            'fonts.feedTitle.family' => 'Courier New',
//            'fonts.feedTitle.style.bold' => true,
//            'fonts.feedTitle.style.italic' => true,
//            'fonts.feedTitle.style.underline' => true,
//
//            'fonts.articleHeadline.size' => 10,
//            'fonts.articleHeadline.family' => 'Courier New',
//            'fonts.articleHeadline.style.bold' => true,
//            'fonts.articleHeadline.style.italic' => true,
//            'fonts.articleHeadline.style.underline' => true,
//
//            'fonts.source.size' => 10,
//            'fonts.source.family' => 'Courier New',
//            'fonts.source.style.bold' => true,
//            'fonts.source.style.italic' => true,
//            'fonts.source.style.underline' => true,
//
//            'fonts.author.size' => 6,
//            'fonts.author.family' => 'Courier New',
//            'fonts.author.style.bold' => true,
//            'fonts.author.style.italic' => true,
//            'fonts.author.style.underline' => true,
//
//            'fonts.date.size' => 14,
//            'fonts.date.family' => 'Courier New',
//            'fonts.date.style.bold' => true,
//            'fonts.date.style.italic' => true,
//            'fonts.date.style.underline' => true,
//
//            'fonts.articleContent.size' => 12,
//            'fonts.articleContent.family' => 'Courier New',
//            'fonts.articleContent.style.bold' => true,
//            'fonts.articleContent.style.italic' => true,
//            'fonts.articleContent.style.underline' => true,
        ]);

        /** @var ConfigurationInterface $configuration */
        $configuration = $this->get(AppBundleServices::CONFIGURATION);

        $feeds = $this->generateFeeds(3);

        $sendableNotification = new SendableNotification(
            SendableNotificationConfig::fromConfiguration($configuration),
            $notification,
            $feeds
        );

        return new Response($sendableNotification->render($this->get('templating')));
    }

    /**
     * Generate notification.
     *
     * @param Notification $notification A Notification instance.
     *
     * @return Notification
     */
    private function generateNotification(Notification $notification)
    {
        $faker = $this->getFaker();

        $user = User::create('some@email.com')
            ->setFirstName('John')
            ->setLastName('Due');

        $defaultOptions = NotificationThemeOptions::createDefault();

        return $notification
            ->setSubject(ucfirst($faker->word))
            ->setName($faker->word)
            ->setOwner($user)
            ->setTheme(NotificationTheme::create()
                ->setName('Some')
                ->setEnhanced($defaultOptions)
                ->setPlain($defaultOptions)
                ->setDefault(true));
    }

    /**
     * @param integer $count Number of generated feeds.
     *
     * @return array[]
     */
    private function generateFeeds($count = 2)
    {
        $feeds = [];
        $faker = $this->getFaker();

        for ($i = 0; $i < $count; ++$i) {
            $documentCount = random_int(1, 3);

            $feeds[] = new FeedData(
                $faker->word,
                $this->generateDocuments($documentCount)
            );
        }

        return $feeds;
    }

    /**
     * Generate documents for notification rendering.
     *
     * @param integer $count Number of documents.
     *
     * @return ArticleDocumentInterface[]
     */
    private function generateDocuments($count)
    {
        $documents = [];
        $generator = new ExternalDocumentGenerator();

        for ($i = 0; $i < $count; ++$i) {
            $documentEntity = $this->generateComments($generator->generate()->toDocumentEntity());
            $data = $documentEntity->getData();
            $data['comments'] = $documentEntity->getComments()->toArray();
            $data['commentsCount'] = count($data['comments']);

            $documents[] = new ArticleDocument(
                $this->strategy,
                $this->strategy->createDocument($data)->getNormalizedData()
            );
        }

        return $documents;
    }

    /**
     * Generate comments for documents.
     *
     * @param Document $document A Document entity instance.
     *
     * @return Document
     */
    private function generateComments(Document $document)
    {
        $faker = $this->getFaker();

        $commentsCount = random_int(0, 3);
        for ($i = 0; $i < $commentsCount; ++$i) {
            $user = User::create($faker->email, '')
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName);

            $document->addComment(new Comment(
                $user,
                $faker->text,
                $faker->optional(0.4, '')->word
            ));
        }

        return $document;
    }

    /**
     * @return \Faker\Generator
     */
    private function getFaker()
    {
        if ($this->faker === null) {
            $this->faker = Factory::create();
        }

        return $this->faker;
    }
}
