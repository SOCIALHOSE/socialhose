<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Notification\NotificationSendHistory;
use UserBundle\Entity\Notification\Schedule\AbstractNotificationSchedule;

/**
 * Class NotificationHistoryFixtures
 * @package AppBundle\DataFixtures\ORM
 */
class NotificationHistoryFixtures extends AbstractFixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager|EntityManagerInterface $manager A ObjectManager
     *                                                      instance.
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        if (! $this->checkEnvironment('dev')) {
            return;
        }

        $notifications = $manager->getRepository(Notification::class)
            ->createQueryBuilder('Notification')
            ->select('Notification, Schedule')
            ->join('Notification.schedules', 'Schedule')
            ->getQuery()
            ->getResult();

        $faker = $this->getFaker();

        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            $max = random_int(15, 40);
            for ($i = 0; $i < $max; ++$i) {
                $schedule = $notification->getSchedules()->toArray();

                $historySchedule = array_map(function (AbstractNotificationSchedule $schedule) {
                    $historySchedule = clone $schedule;
                    $historySchedule->setNotification(null);

                    return $historySchedule;
                }, $faker->randomElements($schedule, random_int(1, count($schedule))));

                $history = new NotificationSendHistory(
                    $notification,
                    $historySchedule
                );
                $history->setDate($faker->dateTimeBetween('- 1 year'));

                $manager->persist($history);
            }

            $manager->flush();
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 6;
    }
}
