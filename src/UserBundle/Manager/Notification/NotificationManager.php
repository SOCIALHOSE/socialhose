<?php

namespace UserBundle\Manager\Notification;

use AppBundle\Configuration\ConfigurationImmutableInterface;
use CacheBundle\Document\Extractor\DocumentContentExtractorInterface;
use CacheBundle\Entity\Comment;
use CacheBundle\Entity\Document;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Entity\Feed\QueryFeed;
use CacheBundle\Feed\Fetcher\Factory\FeedFetcherFactoryInterface;
use CacheBundle\Repository\CommentRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Model\ArticleDocumentInterface;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Notification\NotificationThemeOptions;
use UserBundle\Entity\Recipient\AbstractRecipient;
use UserBundle\Enum\ThemeOptionsUserCommentsEnum;
use UserBundle\Manager\Notification\Computer\NotificationScheduleComputer;
use UserBundle\Manager\Notification\Computer\NotificationScheduleComputerInterface;
use UserBundle\Manager\Notification\Model\FeedData;

/**
 * Class NotificationManager
 * @package UserBundle\Manager\Notification
 */
class NotificationManager implements NotificationManagerInterface
{

    /**
     * Max row's in single insert.
     */
    const BUCKET_SIZE = 100;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var NotificationScheduleComputerInterface
     */
    private $computer;

    /**
     * @var FeedFetcherFactoryInterface
     */
    private $feedFetcherFactory;

    /**
     * @var ConfigurationImmutableInterface
     */
    private $configuration;

    /**
     * @var DocumentContentExtractorInterface
     */
    private $extractor;

    /**
     * NotificationManager constructor.
     *
     * @param EntityManagerInterface            $em                 A EntityManagerInterface
     *                                                              instance.
     * @param FeedFetcherFactoryInterface       $feedFetcherFactory A CacheInterface
     *                                                              instance.
     * @param ConfigurationImmutableInterface   $configuration      A ConfigurationImmutableInterface
     *                                                              instance.
     * @param DocumentContentExtractorInterface $extractor          A DocumentContentExtractorInterface
     *                                                              instance.
     */
    public function __construct(
        EntityManagerInterface $em,
        FeedFetcherFactoryInterface $feedFetcherFactory,
        ConfigurationImmutableInterface $configuration,
        DocumentContentExtractorInterface $extractor
    ) {
        $this->em = $em;
        $this->feedFetcherFactory = $feedFetcherFactory;
        $this->configuration = $configuration;
        $this->extractor = $extractor;

        $this->computer = new NotificationScheduleComputer();
    }

    /**
     * Add new notification or update exists.
     *
     * @param Notification $notification A Notification instance.
     *
     * @return void
     */
    public function persists(Notification $notification)
    {
        /**
         * @param array|\Traversable $collection Filtered collection.
         * @param string             $keyMethod  Method used for getting unique
         *                                       key.
         *
         * @return array
         */
        $unique = static function ($collection, $keyMethod) {
            $unique = [];
            foreach ($collection as $item) {
                $unique[$item->$keyMethod()] = $item;
            }

            return array_values($unique);
        };

        //
        // We should check all schedule's record's and remove duplicates.
        // Same for feeds.
        //

        $schedules = $unique($notification->getSchedules(), 'getKey');
        $feeds = $unique($notification->getFeeds(), 'getId');
        $notification
            ->setSchedules($schedules)
            ->setFeeds($feeds);

        //
        // Persist schedule.
        //
        $this->em->persist($notification);
        $this->em->flush();

        //
        // Get notification id for further processing.
        //
        $id = $notification->getId();

        //
        // We should remove previously computed values.
        //
        $this->removeComputedScheduling($notification->getId());

        if ($notification->isCanBeSent(date_create())) {
            //
            // We should'nt compute render date's if specified notification is can't
            // be sent.
            //
            $timezone = $notification->getTimezone();
            $bound = new \DateTime('+ 1 month');
            $sendUntil = $notification->getSendUntil();
            $bound = (($sendUntil === null) || ($bound <= $sendUntil))
                ? $bound
                : $notification->getSendUntil();

            $dates = $this->computer->compute(
                $schedules,
                $bound->setTimezone($timezone),
                $timezone
            );

            $this->em->getConnection()->transactional(function (Connection $con) use ($id, $dates) {
                $bucket = [];
                $count = 0;
                foreach ($dates as $date) {
                    $bucket[] = sprintf(
                        "('%s', %d, '%s')",
                        $date['date']->format('Y-m-d H:i:s'),
                        $id,
                        implode(',', $date['ids'])
                    );
                    if (++$count === self::BUCKET_SIZE) {
                        $con->executeQuery(
                            'INSERT INTO internal_notification_scheduling (date, notification_id, schedules) VALUES ' .
                            implode(',', $bucket)
                        );
                        $count = 0;
                    }
                }

                if ($count > 0) {
                    $con->executeQuery(
                        'INSERT INTO internal_notification_scheduling (date, notification_id, schedules)  VALUES ' .
                        implode(',', $bucket)
                    );
                }
            });
        }
    }

    /**
     * Activate specified notifications.
     *
     * @param Notification|Notification[] $notifications A activated Notification
     *                                                   entity instance or array
     *                                                   of instances.
     * @param boolean                     $active        Activate or deactivate
     *                                                   specified notifications.
     *
     * @return void
     */
    public function activatedToggle($notifications, $active = true)
    {
        $notifications = $this->normalizeNotifications($notifications);

        foreach ($notifications as $notification) {
            $notification->setActive($active);
            $this->em->persist($notification);
        }

        $this->em->flush();
    }

    /**
     * Publish specified notifications.
     *
     * @param Notification|Notification[] $notifications A activated Notification
     *                                                   entity instance or array
     *                                                   of instances.
     * @param boolean                     $publish       Publish or make private
     *                                                   specified notifications.
     *
     * @return void
     */
    public function publishedToggle($notifications, $publish = true)
    {
        $notifications = $this->normalizeNotifications($notifications);

        foreach ($notifications as $notification) {
            $notification->setPublished($publish);
            $this->em->persist($notification);
        }

        $this->em->flush();
    }

    /**
     * Publish specified notifications.
     *
     * @param AbstractRecipient           $recipient     Who try to subscribe or
     *                                                   unsubscribe from specified
     *                                                   notifications.
     * @param Notification|Notification[] $notifications A Notification entity
     *                                                   instance or array of
     *                                                   instances.
     * @param boolean                     $subscribe     Subscribe or unsubscribe
     *                                                   from specified notifications.
     *
     * @return void
     */
    public function subscriptionToggle(AbstractRecipient $recipient, $notifications, $subscribe = true)
    {
        $notifications = $this->normalizeNotifications($notifications);

        if ($subscribe) {
            //
            // User should not be subscribed to notification twice so we remove
            // all notification on which he already subscribed.
            //
            $checker = \nspl\f\compose(\nspl\f\rpartial('\nspl\a\all', function (AbstractRecipient $checked) use ($recipient) {
                return $checked->getId() !== $recipient->getId();
            }), \nspl\op\methodCaller('getRecipients'));

            $notifications = \nspl\a\filter($checker, $notifications);

            $method = \nspl\op\methodCaller('addRecipient', [ $recipient ]);
        } else {
            $method = \nspl\op\methodCaller('removeRecipient', [ $recipient ]);
        }

        foreach ($notifications as $notification) {
            $method($notification);
            $this->em->persist($notification);
        }
        $this->em->flush();
    }

    /**
     * Remove specified notifications.
     *
     * @param Notification|Notification[] $notifications A removed Notification entity instance.
     *
     * @return void
     */
    public function remove($notifications)
    {
        $notifications = $this->normalizeNotifications($notifications);

        foreach ($notifications as $notification) {
            $this->em->remove($notification);
        }

        $this->removeComputedScheduling(\nspl\a\map(\nspl\op\methodCaller('getId'), $notifications));
        $this->em->flush();
    }

    /**
     * Prepare specified notification for sending.
     *
     * @param Notification $notification A Notification instance.
     *
     * @return SendableNotification
     */
    public function prepareToSend(Notification $notification)
    {
        //
        // We should sync parameters.
        //
        $this->configuration->syncParameters();
        $config = SendableNotificationConfig::fromConfiguration($this->configuration);

        //
        // We should not render notification if it shouldn't be rendered.
        //
        if (! $notification->isCanBeSent(new \DateTime())) {
            return new SendableNotification($config, $notification, [], false);
        }

        //
        // Get used notification theme with applied diff.
        //
        $themeOptions = $notification->getActualThemeOptions();

        /**
         * @param Document $document A Document entity instance.
         *
         * @return ArticleDocumentInterface
         */
        $commentsFetcherFn = $this->createCommentsFetcherFn($themeOptions, $config);

        //
        // Now we should get requested number of documents for every notification
        // feed.
        //
        $feeds = [];
        /** @var AbstractFeed $feed */
        foreach ($notification->getFeeds() as $feed) {
            //
            // Get all documents ids.
            //
            $builder = $this->feedFetcherFactory->get($feed)
                ->createRequestBuilder($feed);

            if (! $builder instanceof SearchRequestBuilderInterface) {
                return new SendableNotification($config, $notification, [], false);
            }

            $filterFactory = $builder->getIndex()->getFilterFactory();
            $lastSentUTC = clone $notification->getLastSentAt();
            $lastSentUTC->setTimezone(new \DateTimeZone('UTC'));

            $documents = $builder
                //
                // We should get documents which were added after last notification
                // sending.
                //
                ->addFilter($filterFactory->gte('date_found', $lastSentUTC->format('c')))
                //
                // Set document limit.
                // This limit is configured by super admin.
                //
                ->setLimit($config->documentsPerFeed)
                ->build()
                ->execute()
                ->getDocuments();

            //
            // Obviously, we should not try to fetch information from database if
            // we don't get any documents.
            //
            if (count($documents) > 0) {
                //
                // Get documents with necessary fields by ids which we fetch from
                // index.
                //
                // Also we should fetch comments, extract content and convert to
                // article instances.
                //
                $extract = $themeOptions->getContent()->getExtract();

                $documents = \nspl\a\map(function (ArticleDocumentInterface $document) use ($commentsFetcherFn, $feed, $extract) {
                    $id = $document->getId();

                    return $document
                        ->mapRawData(function (array $data) use ($commentsFetcherFn, $id) {
                            $data['__comments'] = $commentsFetcherFn($id);
                            $data['__commentsCount'] = count($data['__comments']);

                            return $data;
                        })
                        ->mapNormalizedData(function (array $data) use ($feed, $extract) {
                            $query = '';
                            if ($feed instanceof QueryFeed) {
                                $query = $feed->getQuery()->getRaw();
                            }

                            $result =$this->extractor->extract(
                                $data['content'],
                                $query,
                                $extract,
                                true
                            );

                            $data['content'] = $result->getText() . (
                                mb_strlen($data['content']) < $result->getLength()
                                    ? '...'
                                    : ''
                                );

                            return $data;
                        });
                }, $documents);

                $feeds[] = new FeedData(
                    $feed->getName(),
                    $documents
                );
            }
        }

        //
        // Clear entity manager to avoid memory consuming grow and possible
        // side-effects on flush.
        //
        $this->em->clear();

        return new SendableNotification($config, $notification, $feeds);
    }

    /**
     * @param integer|integer[] $notification A Notification id or array of ids.
     *
     * @return void
     */
    private function removeComputedScheduling($notification)
    {
        $filteredNotifications = array_filter((array) $notification);

        if (count($filteredNotifications) > 0) {
            $this->em->getConnection()->executeQuery(sprintf('
                DELETE FROM internal_notification_scheduling
                WHERE notification_id in (%s)
            ', implode(',', $filteredNotifications)));
        }
    }

    /**
     * Normalize 'notifications' parameter.
     *
     * @param array|object $notifications Passed parameters.
     *
     * @return Notification[]
     */
    private function normalizeNotifications($notifications)
    {
        if ($notifications instanceof Notification) {
            $notifications = [ $notifications ];
        }

        $checkerFn = function ($object) {
            return ! $object instanceof Notification;
        };

        if (! is_array($notifications) || \nspl\a\any($notifications, $checkerFn)) {
            throw new \InvalidArgumentException(sprintf(
                'Expects single %s or array of instances',
                Notification::class
            ));
        }

        return $notifications;
    }

    /**
     * Create proper comment fetcher for current notification.
     *
     * @param NotificationThemeOptions   $options A NotificationThemeOptions
     *                                            instance.
     * @param SendableNotificationConfig $config  A SendableNotificationConfig
     *                                            instance.
     *
     * @return \Closure
     */
    private function createCommentsFetcherFn(
        NotificationThemeOptions $options,
        SendableNotificationConfig $config
    ) {
        $userComments = $options->getContent()->getShowInfo()->getUserComments();

        //
        // We should not fetch comments if notification don't require they.
        //
        if (! $userComments->is(ThemeOptionsUserCommentsEnum::no())) {
            return function (Document $document) {
                return $document;
            };
        }

        /** @var CommentRepository $repository */
        $repository = $this->em->getRepository(Comment::class);

        //
        // Find out which fields do we need for processing current notification.
        //
        $commentFields = [
            'title',
            'content',
        ];
        if ($userComments->is(ThemeOptionsUserCommentsEnum::WITH_AUTHOR_DATE)) {
            $commentFields[] = 'createdAt';
            $commentFields['author'] = [
                'firstName',
                'lastName',
            ];
        }

        //
        // Create proper fetcher.
        //
        return function ($id) use ($repository, $commentFields, $config) {
            return $repository->getListForDocument(
                $id,
                $commentFields,
                $config->commentsPerDocument
            )->getQuery()->getResult();
        };
    }
}
