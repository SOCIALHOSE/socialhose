<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AbstractFixture;
use CacheBundle\Entity\Feed\QueryFeed;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Notification\NotificationTheme;
use UserBundle\Entity\Notification\Schedule\DailyNotificationSchedule;
use UserBundle\Entity\Notification\Schedule\MonthlyNotificationSchedule;
use UserBundle\Entity\Notification\Schedule\WeeklyNotificationSchedule;
use UserBundle\Entity\Recipient\AbstractRecipient;
use UserBundle\Entity\User;
use UserBundle\Enum\AppLimitEnum;
use UserBundle\Enum\NotificationTypeEnum;
use UserBundle\Enum\ThemeOptionExtractEnum;
use UserBundle\Enum\ThemeTypeEnum;

/**
 * Class NotificationFixtures
 * @package AppBundle\DataFixtures\ORM
 */
class NotificationFixtures extends AbstractFixture
{

    private static $scheduleClasses = [
        DailyNotificationSchedule::class,
        WeeklyNotificationSchedule::class,
        MonthlyNotificationSchedule::class,
    ];

    private static $availableDiffMap = [
        NotificationTypeEnum::ALERT => [
            ThemeTypeEnum::PLAIN => [
                'content.extract' => [
                    ThemeOptionExtractEnum::NO,
                    ThemeOptionExtractEnum::START,
                    ThemeOptionExtractEnum::CONTEXT,
                ],
                'content.highlightKeywords.highlight' => [
                    true,
                    false,
                ],
                'content.showInfo.sectionDivider' => [
                    true,
                    false,
                ],
                'content.showInfo.sourceCountry' => [
                    true,
                    false,
                ],
                'content.showInfo.userComments' => [
                    true,
                    false,
                ],
            ],
            ThemeTypeEnum::ENHANCED => [
                'content.extract' => [
                    ThemeOptionExtractEnum::NO,
                    ThemeOptionExtractEnum::START,
                    ThemeOptionExtractEnum::CONTEXT,
                ],
                'content.highlightKeywords.highlight' => [
                    true,
                    false,
                ],
                'content.showInfo.articleSentiment' => [
                    true,
                    false,
                ],
                'content.showInfo.sourceCountry' => [
                    true,
                    false,
                ],
                'content.showInfo.userComments' => [
                    true,
                    false,
                ],
            ],
        ],
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager A ObjectManager instance.
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        switch (true) {
            case $this->checkEnvironment('dev'):
                $this->loadForDevelopment($manager);
                break;

            case $this->checkEnvironment('test'):
                $this->loadForTesting($manager);
                break;
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
    }

    /**
     * @param ObjectManager $manager A ObjectManager instance.
     *
     * @return void
     */
    private function loadForDevelopment(ObjectManager $manager)
    {
        /** @var User $testUser */
        $testUser = $this->getReference('test@email.com');
        /** @var User $masterUser */
        $masterUser = $this->getReference('master@email.com');

        $feeds = [];
        for ($i = 0; $this->hasReference('feed_'. $i); $i++) {
            $feeds[] = $this->getReference('feed_'. $i);
        }

        $this->createNotifications($testUser, $feeds, $manager);
        $this->createNotifications($masterUser, $feeds, $manager);

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager A ObjectManager instance.
     *
     * @return void
     */
    private function loadForTesting(ObjectManager $manager)
    {
        /** @var User $testUser */
        $testUser = $this->getReference('test@email.com');
        /** @var User $masterUser */
        $masterUser = $this->getReference('master@email.com');

        /** @var NotificationTheme $theme */
        $theme = $this->getReference('default_notification_theme');

        /** @var QueryFeed $feedTest1 */
        $feedTest1 = $this->getReference('feed_test1');
        /** @var QueryFeed $feedTest3 */
        $feedTest3 = $this->getReference('feed_test3');

        $notification = Notification::create()
            ->setName('TestUser Notification1')
            ->setSubject('TestUser Notification1 Subject')
            ->setTimezone(new \DateTimeZone($this->getFaker()->timezone))
            ->setOwner($testUser)
            ->setBillingSubscription($testUser->getBillingSubscription())
            ->setTheme($theme)
            ->setNotificationType(NotificationTypeEnum::alert())
            ->setThemeType(ThemeTypeEnum::plain())
            ->setAutomatedSubject(false)
            ->setPublished()
            ->setActive()
            ->setAllowUnsubscribe(false)
            ->setUnsubscribeNotification(true)
            ->setSendWhenEmpty(false)
            ->addFeed($feedTest1)
            ->setSourcesCount(1);
        $manager->persist($notification);

        $notification = Notification::create()
            ->setName('MasterUser Notification1')
            ->setSubject('MasterUser Notification1 Subject')
            ->setTimezone(new \DateTimeZone($this->getFaker()->timezone))
            ->setOwner($masterUser)
            ->setBillingSubscription($testUser->getBillingSubscription())
            ->setTheme($theme)
            ->setNotificationType(NotificationTypeEnum::alert())
            ->setThemeType(ThemeTypeEnum::plain())
            ->setAutomatedSubject(false)
            ->setPublished()
            ->setActive()
            ->setAllowUnsubscribe(false)
            ->setUnsubscribeNotification(true)
            ->setSendWhenEmpty(false)
            ->addFeed($feedTest3)
            ->setSourcesCount(1);
        $manager->persist($notification);

        $manager->flush();
    }

    /**
     * Create notification for specified user.
     *
     * @param User          $user    A User entity instance.
     * @param array         $feeds   Array of feeds.
     * @param ObjectManager $manager A ObjectManager instance.
     *
     * @return void
     */
    private function createNotifications(
        User $user,
        array $feeds,
        ObjectManager $manager
    ) {
        $userFeeds = \app\a\select(\nspl\op\methodCaller('isOwnedBy', [ $user ]), $feeds);
        $userFeedsCount = count($userFeeds);

        if ($userFeedsCount === 0) {
            return;
        }

        $faker = $this->getFaker();

        /** @var NotificationTheme $theme */
        $theme = $this->getReference('default_notification_theme');
        $allowedCount = ceil($user->getAllowedLimit(AppLimitEnum::alerts()) / 2);

        $repository = $manager->getRepository(AbstractRecipient::class);
        $recipients = $repository->findBy([ 'owner' => $user->getId() ]);
        $recipientsCount = count($recipients);

        for ($i = 0; $i < $allowedCount; ++$i) {
            $feeds = $faker->randomElements($userFeeds, random_int(1, ceil($userFeedsCount / 2)));
            $notificationType = NotificationTypeEnum::alert();
            $themeType = new ThemeTypeEnum($faker->randomElement(ThemeTypeEnum::getAvailables()));
            $count = random_int(0, $recipientsCount);

            $notification = Notification::create()
                ->setName($faker->realText($faker->numberBetween(10, 15)))
                ->setSubject($faker->realText(50))
                ->setTimezone(new \DateTimeZone($this->getFaker()->timezone))
                ->setOwner($user)
                ->setBillingSubscription($user->getBillingSubscription())
                ->setTheme($theme)
                ->setNotificationType($notificationType)
                ->setThemeType($themeType)

                ->setAutomatedSubject($faker->boolean(35))
                ->setPublished($faker->boolean(45))
                ->setActive($faker->boolean(85))
                ->setAllowUnsubscribe($faker->boolean(85))
                ->setUnsubscribeNotification($faker->boolean(15))
                ->setSendWhenEmpty($faker->boolean(15));

            if ($themeType->is(ThemeTypeEnum::plain())) {
                $notification->setPlainThemeOptionsDiff(
                    $this->generateDiff($notificationType, $themeType)
                );
            }

            for ($j = 0; $j < $count; ++$j) {
                $notification->addRecipient($recipients[$j]);
            }

            if ($faker->boolean(60)) {
                $notification->setSendUntil($faker->dateTimeBetween('+ 10 days', '+ 1 months'));
            }

            foreach ($feeds as $feed) {
                $notification->addFeed($feed);
            }

            $notification->setSourcesCount(count($feeds));

            $schedules = $this->generateSchedules($faker->numberBetween(1, 4));

            foreach ($schedules as $schedule) {
                $notification->addSchedule($schedule);
                $manager->persist($schedule);
            }

            $user->useLimit(AppLimitEnum::alerts());

            $manager->persist($notification);
            $manager->persist($user);
        }
    }

    /**
     * @param NotificationTypeEnum $notificationType A NotificationTypeEnum instance.
     * @param ThemeTypeEnum        $themeType        A ThemeTypeEnum instance.
     *
     * @return array
     */
    private function generateDiff(
        NotificationTypeEnum $notificationType,
        ThemeTypeEnum $themeType
    ) {
        $faker = $this->getFaker();

        $available = self::$availableDiffMap[(string) $notificationType][(string) $themeType];
        $availableCount = count($available);

        /**
         * @return \Generator
         */
        $generatorFn = function () use ($available, $availableCount, $faker) {
            $availableKeys = array_keys($available);
            $used = [];
            $usedCount = 0;

            while ($availableCount > $usedCount) {
                do {
                    $parameter = $faker->randomElement($availableKeys);
                } while (in_array($parameter, $used, true));

                $used[] = $parameter;
                $usedCount++;

                yield $parameter;
            }
        };

        $parameters = $generatorFn();

        $count = random_int(0, $availableCount - 1);
        $diff = [];

        for ($i = 0; $i < $count; ++$i) {
            $parameter = $parameters->current();
            $diff[$parameter] = $faker->randomElement($available[$parameter]);

            $parameters->next();
        }

        return $diff;
    }

    /**
     * Generate NotificationSchedule entity.
     *
     * @param integer $count How many schedules we need.
     *
     * @return \Generator
     */
    private function generateSchedules($count)
    {
        $faker = $this->getFaker();

        for ($i = 0; $i < $count; $i++) {
            $class = $faker->randomElement(self::$scheduleClasses);
            $methodName = 'create'. \app\c\getShortName($class);
            yield $this->{$methodName}();
        }
    }

    /**
     * @return DailyNotificationSchedule
     *
     * Actually we call it.
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createDailyNotificationSchedule()
    {
        $faker = $this->getFaker();

        return DailyNotificationSchedule::create()
            ->setDays($faker->randomElement(DailyNotificationSchedule::getAvailableDays()))
            ->setTime($faker->randomElement(DailyNotificationSchedule::getAvailableTime()));
    }

    /**
     * @return WeeklyNotificationSchedule
     *
     * Actually we call it.
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createWeeklyNotificationSchedule()
    {
        $faker = $this->getFaker();

        return WeeklyNotificationSchedule::create()
            ->setPeriod($faker->randomElement(WeeklyNotificationSchedule::getAvailablePeriod()))
            ->setDay($faker->randomElement(WeeklyNotificationSchedule::getAvailableDay()))
            ->setHour($faker->randomElement(range(0, 23)))
            ->setMinute($faker->randomElement(range(0, 55, 5)));
    }

    /**
     * @return MonthlyNotificationSchedule
     *
     * Actually we call it.
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function createMonthlyNotificationSchedule()
    {
        $faker = $this->getFaker();

        return MonthlyNotificationSchedule::create()
            ->setDay($faker->randomElement(MonthlyNotificationSchedule::getAvailableDay()))
            ->setHour($faker->randomElement(range(0, 23)))
            ->setMinute($faker->randomElement(range(0, 55, 5)));
    }
}
