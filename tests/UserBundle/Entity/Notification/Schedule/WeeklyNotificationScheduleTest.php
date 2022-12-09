<?php

namespace UserBundle\Entity\Notification\Schedule;

use Doctrine\ORM\Mapping as ORM;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class WeeklyNotificationSchedule
 *
 * @ORM\Entity
 */
class WeeklyNotificationScheduleTest extends TestCase
{

    /**
     * @return void
     */
    public function testComputeDate()
    {
        $schedule = WeeklyNotificationSchedule::create()
            ->setPeriod(WeeklyNotificationSchedule::PERIOD_SECOND)
            ->setDay(WeeklyNotificationSchedule::DAY_FRIDAY)
            ->setHour(12)
            ->setMinute(35);

        $start = date_create()->modify('first day of this month')->setTime(0, 0, 0);
        $end = date_create()->modify('second friday of next month')->setTime(0, 0, 0);

        $dates = $schedule->computeDates($start, $end);

        $this->assertCount(2, $dates);

        $this->assertSame($dates[0]->format('Y-m-d'), date_create()->modify('second friday of this month')->format('Y-m-d'));
        $this->assertSame(12, (int) $dates[0]->format('H'));
        $this->assertSame(35, (int) $dates[0]->format('i'));
        $this->assertSame($dates[1]->format('Y-m-d'), date_create()->modify('second friday of next month')->format('Y-m-d'));
        $this->assertSame(12, (int) $dates[1]->format('H'));
        $this->assertSame(35, (int) $dates[1]->format('i'));


        $schedule = WeeklyNotificationSchedule::create()
            ->setPeriod(WeeklyNotificationSchedule::PERIOD_FIRST)
            ->setDay(WeeklyNotificationSchedule::DAY_TUESDAY);

        $start = date_create()->modify('first day of this month')->setTime(0, 0, 0);
        $end = date_create()->modify('second friday of next month')->setTime(0, 0, 0);

        $dates = $schedule->computeDates($start, $end);

        $this->assertCount(2, $dates);

        $this->assertSame($dates[0]->format('Y-m-d'), date_create()->modify('first tuesday of this month')->format('Y-m-d'));
        $this->assertSame(0, (int) $dates[0]->format('H'));
        $this->assertSame(0, (int) $dates[0]->format('i'));
        $this->assertSame($dates[1]->format('Y-m-d'), date_create()->modify('first tuesday of next month')->format('Y-m-d'));
        $this->assertSame(0, (int) $dates[1]->format('H'));
        $this->assertSame(0, (int) $dates[1]->format('i'));
    }

    /**
     * @return void
     */
    public function testComputeDateEvery()
    {
        $schedule = WeeklyNotificationSchedule::create()
            ->setPeriod(WeeklyNotificationSchedule::PERIOD_EVERY)
            ->setDay(WeeklyNotificationSchedule::DAY_MONDAY)
            ->setHour(10)
            ->setMinute(45);

        $start = date_create()->modify('first day of this month')->setTime(0, 0, 0);
        $end = date_create()->modify('last day of next month')->setTime(0, 0, 0);

        $dates = $schedule->computeDates($start, $end);

        /** @var \DateTime $date */
        foreach ($dates as $date) {
            $this->assertSame(
                1,
                (int) $date->format('N')
            );
            $this->assertSame(10, (int) $date->format('H'));
            $this->assertSame(45, (int) $date->format('i'));
        }
    }

    /**
     * @return void
     */
    public function testComputeDateLast()
    {
        $schedule = WeeklyNotificationSchedule::create()
            ->setPeriod(WeeklyNotificationSchedule::PERIOD_LAST)
            ->setDay(WeeklyNotificationSchedule::DAY_SUNDAY)
            ->setHour(10)
            ->setMinute(45);

        $start = date_create()->modify('first day of this month')->setTime(0, 0, 0);
        $end = date_create()->modify('next month')->modify('last day of next month')->setTime(0, 0, 0);

        $dates = $schedule->computeDates($start, $end);

        $this->assertCount(3, $dates);

        $this->assertSame($dates[0]->format('Y-m-d'), date_create()->modify('last sunday of this month')->format('Y-m-d'));
        $this->assertSame(10, (int) $dates[0]->format('H'));
        $this->assertSame(45, (int) $dates[0]->format('i'));
        $this->assertSame($dates[1]->format('Y-m-d'), date_create()->modify('last sunday of next month')->format('Y-m-d'));
        $this->assertSame(10, (int) $dates[1]->format('H'));
        $this->assertSame(45, (int) $dates[1]->format('i'));
        $this->assertSame($dates[2]->format('Y-m-d'), date_create()->modify('next month')->modify('last sunday of next month')->format('Y-m-d'));
        $this->assertSame(10, (int) $dates[2]->format('H'));
        $this->assertSame(45, (int) $dates[2]->format('i'));
    }
}
