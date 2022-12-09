<?php

namespace UserBundle\Entity\Notification\Schedule;

use Doctrine\ORM\Mapping as ORM;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MonthlyNotificationScheduleTest
 *
 * @ORM\Entity
 */
class MonthlyNotificationScheduleTest extends TestCase
{

    /**
     * @return void
     */
    public function testComputeDate()
    {
        $schedule = MonthlyNotificationSchedule::create()
            ->setDay(5)
            ->setHour(12)
            ->setMinute(35);

        $start = date_create()->modify('first day of this month')->setTime(0, 0, 0);
        $end = date_create()->modify('last day of next month')->setTime(0, 0, 0);

        $dates = $schedule->computeDates($start, $end);

        $this->assertCount(2, $dates);

        $this->assertSame($dates[0]->format('Y-m-d'), date_create()->modify('first day of this month')->modify('4 day')->format('Y-m-d'));
        $this->assertSame(5, (int) $dates[0]->format('j'));
        $this->assertSame(12, (int) $dates[0]->format('H'));
        $this->assertSame(35, (int) $dates[0]->format('i'));
        $this->assertSame($dates[1]->format('Y-m-d'), date_create()->modify('first day of next month')->modify('4 day')->format('Y-m-d'));
        $this->assertSame(5, (int) $dates[1]->format('j'));
        $this->assertSame(12, (int) $dates[1]->format('H'));
        $this->assertSame(35, (int) $dates[1]->format('i'));

        $schedule = MonthlyNotificationSchedule::create()
            ->setDay(2)
            ->setHour(8)
            ->setMinute(50);

        $start = date_create()->modify('first day of this month')->modify('1 day')->setTime(0, 0, 0);
        $end = date_create()->modify('next month')->modify('first day of next month')->modify('1 day')->setTime(0, 0, 0);

        $dates = $schedule->computeDates($start, $end);

        $this->assertCount(3, $dates);

        $this->assertSame($dates[0]->format('Y-m-d'), date_create()->modify('first day of this month')->modify('1 day')->format('Y-m-d'));
        $this->assertSame(2, (int) $dates[0]->format('j'));
        $this->assertSame(8, (int) $dates[0]->format('H'));
        $this->assertSame(50, (int) $dates[0]->format('i'));
        $this->assertSame($dates[1]->format('Y-m-d'), date_create()->modify('first day of next month')->modify('1 day')->format('Y-m-d'));
        $this->assertSame(2, (int) $dates[1]->format('j'));
        $this->assertSame(8, (int) $dates[1]->format('H'));
        $this->assertSame(50, (int) $dates[1]->format('i'));
        $this->assertSame($dates[2]->format('Y-m-d'), date_create()->modify('next month')->modify('first day of next month')->modify('1 day')->format('Y-m-d'));
        $this->assertSame(2, (int) $dates[2]->format('j'));
        $this->assertSame(8, (int) $dates[2]->format('H'));
        $this->assertSame(50, (int) $dates[2]->format('i'));
    }

    /**
     * @return void
     */
    public function testComputeDateLast()
    {
        $schedule = MonthlyNotificationSchedule::create()
            ->setDay(MonthlyNotificationSchedule::DAY_LAST)
            ->setHour(12)
            ->setMinute(35);

        $start = date_create()->modify('first day of this month')->setTime(0, 0, 0);
        $end = date_create()->modify('last day of next month')->setTime(0, 0, 0);

        $dates = $schedule->computeDates($start, $end);

        $this->assertCount(2, $dates);

        $this->assertSame($dates[0]->format('Y-m-d'), date_create()->modify('last day of this month')->format('Y-m-d'));
        $this->assertSame(12, (int) $dates[0]->format('H'));
        $this->assertSame(35, (int) $dates[0]->format('i'));
        $this->assertSame($dates[1]->format('Y-m-d'), date_create()->modify('last day of next month')->format('Y-m-d'));
        $this->assertSame(12, (int) $dates[1]->format('H'));
        $this->assertSame(35, (int) $dates[1]->format('i'));

        $schedule = MonthlyNotificationSchedule::create()
            ->setDay(MonthlyNotificationSchedule::DAY_LAST)
            ->setHour(2)
            ->setMinute(15);

        $start = date_create()->modify('last day of this month')->setTime(0, 0, 0);
        $end = date_create()->modify('first day of next month')->setTime(0, 0, 0);

        $dates = $schedule->computeDates($start, $end);

        $this->assertCount(1, $dates);

        $this->assertSame($dates[0]->format('Y-m-d'), date_create()->modify('last day of this month')->format('Y-m-d'));
        $this->assertSame(2, (int) $dates[0]->format('H'));
        $this->assertSame(15, (int) $dates[0]->format('i'));
    }
}
