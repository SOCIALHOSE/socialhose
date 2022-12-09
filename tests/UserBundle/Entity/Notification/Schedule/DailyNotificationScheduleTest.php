<?php

namespace UserBundle\Entity\Notification\Schedule;

use Doctrine\ORM\Mapping as ORM;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class DailyNotificationScheduleTest
 */
class DailyNotificationScheduleTest extends TestCase
{

    /**
     * @return void
     */
    public function testComputeDate()
    {
        $schedule = DailyNotificationSchedule::create()
            ->setDays(DailyNotificationSchedule::DAYS_ALL)
            ->setTime(DailyNotificationSchedule::TIME_30_M);

        $start = new \DateTime();
        $start->setTime($start->format('H'), $start->format('i'), 0);
        $end = clone $start;
        $end->modify('+ 1 day');

        $dates = $schedule->computeDates($start, $end);

        /** @var \DateTime $date */
        $checkStart = clone $start;
        foreach ($dates as $date) {
            $this->assertSame(
                $checkStart->modify('+ 30 minute')->format('c'),
                $date->format('c')
            );
        }
    }

    /**
     * @return void
     */
    public function testComputeDateWeekends()
    {
        $schedule = DailyNotificationSchedule::create()
            ->setDays(DailyNotificationSchedule::DAYS_WEEKENDS)
            ->setTime(DailyNotificationSchedule::TIME_4_H);

        $start = new \DateTime();
        $start->modify('first friday');
        $start->setTime(23, 0, 0);
        $end = clone $start;
        $end->modify('+ 1 day');

        $dates = $schedule->computeDates($start, $end);

        /** @var \DateTime $date */
        $checkStart = clone $start;
        foreach ($dates as $date) {
            $this->assertSame(
                $checkStart->modify('+ 4 hour')->format('c'),
                $date->format('c')
            );
            $this->assertLessThanOrEqual((int) $date->format('N'), 5);
        }
    }

    /**
     * @return void
     */
    public function testComputeDateWeekdays()
    {
        $schedule = DailyNotificationSchedule::create()
            ->setDays(DailyNotificationSchedule::DAYS_WEEKDAYS)
            ->setTime(DailyNotificationSchedule::TIME_1_H);

        $start = new \DateTime();
        $start->modify('first sunday');
        $start->setTime(23, 0, 0);
        $end = clone $start;
        $end->modify('+ 1 day');

        $dates = $schedule->computeDates($start, $end);

        /** @var \DateTime $date */
        $checkStart = clone $start;
        foreach ($dates as $date) {
            $this->assertSame(
                $checkStart->modify('+ 1 hour')->format('c'),
                $date->format('c')
            );
            $this->assertGreaterThan((int) $date->format('N'), 5);
        }
    }

    /**
     * @return void
     */
    public function testComputeDateWithSpecifiedDates()
    {
        $schedule = DailyNotificationSchedule::create()
            ->setDays(DailyNotificationSchedule::DAYS_ALL)
            ->setTime(DailyNotificationSchedule::TIME_15_M);

        $start = new \DateTime();
        $start
            ->setDate(2017, 6, 9)
            ->setTime(0, 0, 0);
        $end = clone $start;
        $end->modify('+ 30 minutes');

        $dates = $schedule->computeDates($start, $end);

        $this->assertCount(2, $dates);
        $this->assertSame('2017-06-09 00:15:00', $dates[0]->format('Y-m-d H:i:s'));
        $this->assertSame('2017-06-09 00:30:00', $dates[1]->format('Y-m-d H:i:s'));
    }
}
